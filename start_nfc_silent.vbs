Set WshShell = CreateObject("WScript.Shell")
WshShell.CurrentDirectory = "D:\xamp\htdocs\CodeCommanders"
WshShell.Run "D:\xamp\htdocs\CodeCommanders\.venv\Scripts\pythonw.exe ""D:\xamp\htdocs\CodeCommanders\nfc_bridge.py""", 0, False