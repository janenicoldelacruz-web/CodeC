import ctypes
from ctypes import wintypes
import time

# Windows Smart Card API (WinSCard.dll)
winscard = ctypes.windll.winscard

# 64-bit Windows Data Types
SCARDCONTEXT = ctypes.c_size_t
SCARDHANDLE  = ctypes.c_size_t
DWORD        = wintypes.DWORD
LONG         = wintypes.LONG

SCARD_SCOPE_USER       = 0
SCARD_SHARE_SHARED     = 2
SCARD_PROTOCOL_T0      = 1
SCARD_PROTOCOL_T1      = 2
SCARD_LEAVE_CARD       = 0
SCARD_S_SUCCESS        = 0

class SCARD_IO_REQUEST(ctypes.Structure):
    _fields_ = [("dwProtocol", DWORD), ("cbPciLength", DWORD)]

# Setup Windows API Definitions
winscard.SCardEstablishContext.argtypes = [DWORD, ctypes.c_void_p, ctypes.c_void_p, ctypes.POINTER(SCARDCONTEXT)]
winscard.SCardEstablishContext.restype  = LONG

winscard.SCardListReadersA.argtypes = [SCARDCONTEXT, ctypes.c_char_p, ctypes.c_char_p, ctypes.POINTER(DWORD)]
winscard.SCardListReadersA.restype  = LONG

winscard.SCardConnectA.argtypes = [SCARDCONTEXT, ctypes.c_char_p, DWORD, DWORD, ctypes.POINTER(SCARDHANDLE), ctypes.POINTER(DWORD)]
winscard.SCardConnectA.restype  = LONG

winscard.SCardTransmit.argtypes = [SCARDHANDLE, ctypes.POINTER(SCARD_IO_REQUEST), ctypes.POINTER(ctypes.c_ubyte), DWORD, ctypes.c_void_p, ctypes.POINTER(ctypes.c_ubyte), ctypes.POINTER(DWORD)]
winscard.SCardTransmit.restype  = LONG

winscard.SCardDisconnect.argtypes = [SCARDHANDLE, DWORD]
winscard.SCardDisconnect.restype  = LONG

def main():
    print("\n=======================================================")
    print("       ACS ACR122U NFC READER & CARD HARDWARE TEST     ")
    print("=======================================================\n")

    # 1. Test Windows Smart Card Service
    hContext = SCARDCONTEXT()
    ret = winscard.SCardEstablishContext(SCARD_SCOPE_USER, None, None, ctypes.byref(hContext))
    if ret != SCARD_S_SUCCESS:
        print(f"[X] FAILED: Windows Smart Card Service error (Code: 0x{ret & 0xFFFFFFFF:08X})")
        print("    -> Pakisigurado na tumatakbo ang 'Smart Card' service sa Windows services.msc.")
        return
    print("[✓] Step 1: Windows Smart Card Service is RUNNING.")

    # 2. Test USB Reader Detection
    pcchReaders = DWORD()
    winscard.SCardListReadersA(hContext, None, None, ctypes.byref(pcchReaders))
    if pcchReaders.value == 0:
        print("[X] FAILED: Walang na-detect na ACR122U Reader.")
        print("    -> Pakisaksak nang maayos ang USB cable ng ACR122U.")
        return

    mszReaders = ctypes.create_string_buffer(pcchReaders.value)
    winscard.SCardListReadersA(hContext, None, mszReaders, ctypes.byref(pcchReaders))
    reader_name = mszReaders.raw.decode('latin1').split('\x00')[0]
    print(f"[✓] Step 2: ACR122U Reader DETECTED: '{reader_name}'")

    print("\n-------------------------------------------------------")
    print(">>> HANDA NA! ILAPAT ANG PUTING NFC CARD SA READER <<<")
    print("-------------------------------------------------------\n")

    last_scanned_uid = None

    while True:
        hCard = SCARDHANDLE()
        dwActiveProtocol = DWORD()

        # Connect to card
        ret_conn = winscard.SCardConnectA(
            hContext,
            reader_name.encode('latin1'),
            SCARD_SHARE_SHARED,
            SCARD_PROTOCOL_T0 | SCARD_PROTOCOL_T1,
            ctypes.byref(hCard),
            ctypes.byref(dwActiveProtocol)
        )

        if ret_conn == SCARD_S_SUCCESS:
            pci = SCARD_IO_REQUEST(dwActiveProtocol.value, 8)
            
            # Subukan ang mga standard APDU commands para kunin ang Card UID
            apdu_list = [
                ([0xFF, 0xCA, 0x00, 0x00, 0x00], "Standard 4-byte/7-byte UID"),
                ([0xFF, 0xCA, 0x00, 0x00, 0x04], "Mifare Classic Le=4"),
                ([0xFF, 0xCA, 0x00, 0x00, 0x07], "Mifare Ultralight Le=7")
            ]

            card_found = False
            for apdu, desc in apdu_list:
                cmd = (ctypes.c_ubyte * len(apdu))(*apdu)
                pbRecv = (ctypes.c_ubyte * 256)()
                pcbRecvLength = DWORD(256)

                ret_trans = winscard.SCardTransmit(
                    hCard,
                    ctypes.byref(pci),
                    cmd,
                    len(apdu),
                    None,
                    pbRecv,
                    ctypes.byref(pcbRecvLength)
                )

                if ret_trans == SCARD_S_SUCCESS and pcbRecvLength.value >= 2:
                    raw_bytes = list(pbRecv[:pcbRecvLength.value])
                    sw1, sw2 = raw_bytes[-2], raw_bytes[-1]
                    
                    if sw1 == 0x90 and sw2 == 0x00:
                        uid_bytes = raw_bytes[:-2]
                        card_uid = "".join([f"{b:02X}" for b in uid_bytes])
                        formatted_uid = ":".join([f"{b:02X}" for b in uid_bytes])

                        if card_uid != last_scanned_uid:
                            last_scanned_uid = card_uid
                            print("==================================================")
                            print(f" [★] SUCCESS! NFC CARD NABASA NANG MAAYOS!")
                            print(f"     -> Raw Hex UID    : {card_uid}")
                            print(f"     -> Formatted UID  : {formatted_uid}")
                            print(f"     -> Byte Length    : {len(uid_bytes)} bytes")
                            print(f"     -> Protocol       : {'T=0' if dwActiveProtocol.value == 1 else 'T=1'}")
                            print("==================================================\n")
                        
                        card_found = True
                        break

            winscard.SCardDisconnect(hCard, SCARD_LEAVE_CARD)

        else:
            # Walang card na nakapatong
            last_scanned_uid = None

        time.sleep(0.2)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n[!] Test stopped by user.")
