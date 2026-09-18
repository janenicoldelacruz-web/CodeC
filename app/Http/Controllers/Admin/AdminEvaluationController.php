<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class AdminEvaluationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $sectionFilter = $request->query('section');

        // Fetch active academic period or fallback
        $activePeriod = Schema::hasTable('academic_periods')
            ? DB::table('academic_periods')->where('is_active', 1)->first()
            : null;

        $activeSchoolYear = $activePeriod->school_year ?? '2026-2027';

        // Fetch sections for filters if table exists
        $sections = Schema::hasTable('academic_sections')
            ? DB::table('academic_sections')->pluck('section_name')->toArray()
            : [];

        // KPI metrics
        $totalFaculty = User::where('role_id', 2)->count(); // Role 2 = Faculty/Teacher
        $totalEvaluations = Schema::hasTable('peer_evaluations') ? DB::table('peer_evaluations')->count() : 0;
        
        $averageScore = Schema::hasTable('peer_evaluations') 
            ? DB::table('peer_evaluations')->avg('average_score') ?? 0.0 
            : 0.0;

        $totalStudents = User::where('role_id', 3)->count(); // Role 3 = Student
        $evalProgress = $totalStudents > 0 ? min(100, round(($totalEvaluations / max(1, $totalStudents * $totalFaculty)) * 100)) : 0;

        return view('admin.evaluations.index', compact(
            'activeSchoolYear',
            'sections',
            'totalEvaluations',
            'totalFaculty',
            'averageScore',
            'evalProgress'
        ));
    }

    public function seedOfficialQuestions($type = null)
    {
        $now = now();
        
        $typesToSeed = $type ? [$type] : ['principal', 'peer', 'student', 'self'];

        foreach ($typesToSeed as $t) {
            DB::table('evaluation_questions')->where('form_type', $t)->delete();
            $records = [];

            if ($t === 'principal') {
                $items = [
                    ['I. Instructional Competence (50% Weight)', 'formulates objectives of lesson plan'],
                    ['I. Instructional Competence (50% Weight)', 'prepares appropriate teaching aids'],
                    ['I. Instructional Competence (50% Weight)', 'knowledge in teaching methods, strategies and techniques'],
                    ['I. Instructional Competence (50% Weight)', 'utilizes the art of questioning to develop higher level of thinking'],
                    ['I. Instructional Competence (50% Weight)', 'conveys ideas clearly'],
                    ['I. Instructional Competence (50% Weight)', 'ensures students\' participation'],
                    ['I. Instructional Competence (50% Weight)', 'recognizes individual differences'],
                    ['I. Instructional Competence (50% Weight)', 'shows enthusiasm in teaching'],
                    ['I. Instructional Competence (50% Weight)', 'shows mastery of the subject matter'],
                    ['I. Instructional Competence (50% Weight)', 'diagnoses learners\' needs'],
                    ['I. Instructional Competence (50% Weight)', 'knowledge in constructing test questions'],
                    ['I. Instructional Competence (50% Weight)', 'maintains classroom conducive to learning'],
                    ['I. Instructional Competence (50% Weight)', 'prepares and utilizes learners\' records effectively'],
                    ['I. Instructional Competence (50% Weight)', 'encourages parents\' involvement in school programs and activities'],
                    ['I. Instructional Competence (50% Weight)', 'exercises parental responsibility to the learners'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Obedience to institutional policies and lawful directives'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Honesty & integrity in all professional dealings'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Dedication & commitment to teaching mission'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Initiative and self-direction in task fulfillment'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Courtesy and politeness to peers, students, and visitors'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Human relations and interpersonal rapport'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Leadership and positive influence in the school community'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Stress management and emotional stability'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Cooperation with administrative programs and committees'],
                    ['II. Professional & Personal Characteristics (30% Weight)', 'Proper attire and professional grooming'],
                    ['III. Participation & Attendance (20% Weight)', 'Punctuality in class reporting and school events'],
                    ['III. Participation & Attendance (20% Weight)', 'Regularity of attendance and prompt submission of school forms']
                ];
                foreach ($items as $i => $item) {
                    $records[] = ['form_type' => 'principal', 'category' => $item[0], 'question' => $item[1], 'order_num' => $i + 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
                }
            } elseif ($t === 'peer') {
                $items = [
                    'Shares relevant up-to-date ideas during faculty meetings',
                    'Shares ideas with other members of the unit on how to teach a subject.',
                    'Volunteers to participate in committee work.',
                    'Accepts responsibilities willingly.',
                    'Attends faculty meetings and other school activities regularly and promptly.',
                    'Accomplishes assigned reports and other tasks accurately and promptly as member of a committee.',
                    'Possesses pleasant disposition and helps maintain esprit de corps by relating harmoniously with colleagues, administrative staff, and students.',
                    'Communicates ideas clearly and accurately.',
                    'Shows open-mindedness by respecting the ideas of others.',
                    'Shows intellectual honesty by giving due recognition to the works of others.',
                    'Shows concern and sincerity in dealing with others.',
                    'Respects dignity of others by not speaking ill of them.',
                    'Helps maintain an academic atmosphere in the unit and school as a whole.',
                    'Shows evidence of commitment to the mission of the school.',
                    'In sum, tries to exhibit exemplary behavior of a true professional.'
                ];
                foreach ($items as $i => $q) {
                    $records[] = ['form_type' => 'peer', 'category' => 'Faculty Peer Collaboration & Professionalism', 'question' => $q, 'order_num' => $i + 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
                }
            } elseif ($t === 'student') {
                $items = [
                    ['A. Mastery of Subject Matter', 'Discusses/Elaborates/Explains the lesson thoroughly without directly reading from books.'],
                    ['A. Mastery of Subject Matter', 'Provides adequate and relevant examples and demonstrations to illustrate concepts and skills.'],
                    ['A. Mastery of Subject Matter', 'Cites, relates, ties up lessons with other disciplines/subjects, when applicable.'],
                    ['A. Mastery of Subject Matter', 'Answers students\' questions/clarifications clearly.'],
                    ['A. Mastery of Subject Matter', 'Discusses considerable coverage of topics per session within learning capabilities.'],
                    ['B. Communication Skills', 'Communicates in clear, correct and coherent language suited to student level.'],
                    ['B. Communication Skills', 'Shifts to vernacular WHEN NECESSARY for clearer communication.'],
                    ['B. Communication Skills', 'Uses language that inspires students to listen.'],
                    ['B. Communication Skills', 'Speaks at appropriate speed and volume.'],
                    ['B. Communication Skills', 'Exhibits appropriate facial expressions, gestures, and eye contact.'],
                    ['C. Classroom Management', 'Capable of maintaining classroom discipline.'],
                    ['C. Classroom Management', 'Begins the class on time and does not dismiss before time.'],
                    ['C. Classroom Management', 'Provides an environment pleasant and conducive to learning.'],
                    ['C. Classroom Management', 'Holds the attention/interest of students and responds to reactions.'],
                    ['C. Classroom Management', 'Encourages student participation and interaction.'],
                    ['D. Teaching Methodology', 'Uses appropriate teaching strategies, aids, devices and/or technology.'],
                    ['D. Teaching Methodology', 'Asks relevant questions that bring about critical thinking.'],
                    ['D. Teaching Methodology', 'Relates lessons to current situations and integrates values.'],
                    ['D. Teaching Methodology', 'Gives quizzes, written assignments, and recitations regularly.'],
                    ['D. Teaching Methodology', 'Recognizes student classroom participation.'],
                    ['E. Teacher Professional & Personal Qualities', 'Always present for class in prescribed school uniform.'],
                    ['E. Teacher Professional & Personal Qualities', 'Respectable and dignified in actions and words.'],
                    ['E. Teacher Professional & Personal Qualities', 'Observes professional ethics in dealing with students.'],
                    ['E. Teacher Professional & Personal Qualities', 'Makes himself/herself available for student consultation.'],
                    ['E. Teacher Professional & Personal Qualities', 'Treats students fairly.']
                ];
                foreach ($items as $i => $item) {
                    $records[] = ['form_type' => 'student', 'category' => $item[0], 'question' => $item[1], 'order_num' => $i + 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
                }
            } elseif ($t === 'self') {
                $items = [
                    ['I. Teaching Performance & Delivery', 'I regularly reflect on my teaching effectiveness and instructional outcomes.'],
                    ['I. Teaching Performance & Delivery', 'I design lesson plans that clearly target learning competencies and student needs.'],
                    ['I. Teaching Performance & Delivery', 'I employ interactive teaching methods and relevant educational technology.'],
                    ['II. Professional Growth & Development', 'I actively engage in professional development activities, seminars, and training.'],
                    ['II. Professional Growth & Development', 'I continuously update my knowledge and skills in my field of specialization.'],
                    ['III. Professional Conduct & Ethics', 'I consistently adhere to institutional policies, core values, and ethical standards.'],
                    ['III. Professional Conduct & Ethics', 'I maintain positive, collaborative, and professional rapport with peers and superiors.'],
                    ['III. Professional Conduct & Ethics', 'I demonstrate punctuality, accountability, and dedication to institutional duties.']
                ];
                foreach ($items as $i => $item) {
                    $records[] = ['form_type' => 'self', 'category' => $item[0], 'question' => $item[1], 'order_num' => $i + 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
                }
            }

            if (!empty($records)) {
                DB::table('evaluation_questions')->insert($records);
            }
        }
    }

    public function periods(Request $request)
    {
        if (!Schema::hasTable('evaluation_questions')) {
            Schema::create('evaluation_questions', function ($table) {
                $table->id();
                $table->string('form_type')->default('principal');
                $table->string('category');
                $table->text('question');
                $table->integer('order_num')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
            $this->seedOfficialQuestions();
        }

        $activePeriod = Schema::hasTable('academic_periods')
            ? DB::table('academic_periods')->where('is_active', 1)->first()
            : null;

        $selectedType = $request->query('type', 'principal');

        // Check if currently requested form has less than minimum standard items; if so, populate automatically
        $count = DB::table('evaluation_questions')->where('form_type', $selectedType)->count();
        if ($count < 5) {
            $this->seedOfficialQuestions($selectedType);
        }

        $allQuestions = DB::table('evaluation_questions')
            ->where('form_type', $selectedType)
            ->orderBy('order_num', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $groupedQuestions = $allQuestions->groupBy('category');

        $counts = [
            'principal' => DB::table('evaluation_questions')->where('form_type', 'principal')->count(),
            'peer'      => DB::table('evaluation_questions')->where('form_type', 'peer')->count(),
            'student'   => DB::table('evaluation_questions')->where('form_type', 'student')->count(),
            'self'      => DB::table('evaluation_questions')->where('form_type', 'self')->count(),
        ];

        return view('admin.evaluations.periods', compact('activePeriod', 'allQuestions', 'groupedQuestions', 'selectedType', 'counts'));
    }

    public function resetQuestions(Request $request)
    {
        $type = $request->input('form_type', 'principal');
        $this->seedOfficialQuestions($type);
        return back()->with('success', 'Official SIA evaluation rubric has been restored successfully!');
    }

    public function savePeriod(Request $request)
    {
        $request->validate([
            'semester'    => 'required|string|max:100',
            'school_year' => 'required|string|max:50',
            'status'      => 'required|in:open,closed,OPEN,CLOSED'
        ]);

        $statusNormalized = strtolower($request->status);
        $isOpen = ($statusNormalized === 'open');

        Cache::put('evaluations_open', $isOpen);

        if (Schema::hasTable('academic_periods')) {
            $updateData = [
                'semester'    => $request->semester,
                'school_year' => $request->school_year,
                'updated_at'  => now()
            ];
            
            if (Schema::hasColumn('academic_periods', 'status')) {
                $updateData['status'] = $statusNormalized;
            }

            DB::table('academic_periods')->where('is_active', 1)->update($updateData);
        }

        return back()->with('success', 'Appraisal period cycle and evaluation status updated successfully!');
    }

    public function toggleStatus(Request $request)
    {
        $currentStatus = Cache::get('evaluations_open', false);
        $newStatus = !$currentStatus;

        Cache::put('evaluations_open', $newStatus);

        return back()->with('success', 'Evaluation window status successfully toggled to ' . ($newStatus ? 'OPEN' : 'CLOSED') . '!');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'form_type' => 'required|string|in:principal,peer,student,self',
            'category'  => 'required|string|max:255',
            'question'  => 'required|string',
        ]);

        $maxOrder = DB::table('evaluation_questions')->where('form_type', $request->form_type)->max('order_num') ?? 0;

        DB::table('evaluation_questions')->insert([
            'form_type'  => $request->form_type,
            'category'   => $request->category,
            'question'   => $request->question,
            'order_num'  => $maxOrder + 1,
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Question item added successfully!');
    }

    public function updateQuestion(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'question' => 'required|string',
        ]);

        DB::table('evaluation_questions')->where('id', $id)->update([
            'category'   => $request->category,
            'question'   => $request->question,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Question updated successfully!');
    }

    public function destroyQuestion($id)
    {
        DB::table('evaluation_questions')->where('id', $id)->delete();
        return back()->with('success', 'Question deleted successfully!');
    }

    public function results(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $query = User::where('role_id', 2);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $allFaculty = $query->orderBy('last_name', 'asc')->get();

        $totalSubmissions = 0;
        $facultyMetrics = $allFaculty->map(function($teacher) use (&$totalSubmissions) {
            $peerEvals = Schema::hasTable('peer_evaluations')
                ? DB::table('peer_evaluations')->where('evaluatee_id', $teacher->id)->get()
                : collect([]);

            $peerCount = $peerEvals->count();
            $totalSubmissions += $peerCount;
            $peerAvg = $peerCount > 0 ? round($peerEvals->avg('average_score'), 2) : null;
            $comments = $peerEvals->whereNotNull('comments')->pluck('comments')->filter()->values();

            $descriptor = 'Pending';
            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';

            if ($peerAvg !== null) {
                if ($peerAvg >= 4.50) {
                    $descriptor = 'Outstanding';
                    $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200';
                } elseif ($peerAvg >= 3.50) {
                    $descriptor = 'Very Satisfactory';
                    $badgeClass = 'bg-blue-50 text-blue-800 border-blue-200';
                } elseif ($peerAvg >= 2.50) {
                    $descriptor = 'Satisfactory';
                    $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                } else {
                    $descriptor = 'Needs Improvement';
                    $badgeClass = 'bg-red-50 text-red-800 border-red-200';
                }
            }

            return (object) [
                'id'          => $teacher->id,
                'name'        => 'Prof. ' . $teacher->first_name . ' ' . $teacher->last_name,
                'short_name'  => $teacher->last_name . ', ' . substr($teacher->first_name, 0, 1) . '.',
                'first_name'  => $teacher->first_name,
                'last_name'   => $teacher->last_name,
                'email'       => $teacher->email,
                'id_number'   => $teacher->id_number ?? 'N/A',
                'peer_count'  => $peerCount,
                'peer_avg'    => $peerAvg ? (float)$peerAvg : null,
                'descriptor'  => $descriptor,
                'badge_class' => $badgeClass,
                'comments'    => $comments,
            ];
        });

        $totalFaculty = $facultyMetrics->count();
        $totalEvaluated = $facultyMetrics->filter(fn($f) => $f->peer_count > 0)->count();
        $completionRate = $totalFaculty > 0 ? round(($totalEvaluated / $totalFaculty) * 100, 1) : 0;
        
        $scoredTeachers = $facultyMetrics->filter(fn($f) => $f->peer_avg !== null);
        $overallInstMean = $scoredTeachers->count() > 0 ? number_format($scoredTeachers->avg('peer_avg'), 2) : '0.00';
        $highestScore = $scoredTeachers->count() > 0 ? number_format($scoredTeachers->max('peer_avg'), 2) : '--';
        $lowestScore = $scoredTeachers->count() > 0 ? number_format($scoredTeachers->min('peer_avg'), 2) : '--';

        $distOutstanding = $facultyMetrics->filter(fn($f) => $f->peer_avg >= 4.50)->count();
        $distVerySat = $facultyMetrics->filter(fn($f) => $f->peer_avg >= 3.50 && $f->peer_avg < 4.50)->count();
        $distSat = $facultyMetrics->filter(fn($f) => $f->peer_avg >= 2.50 && $f->peer_avg < 3.50)->count();
        $distNeedsImp = $facultyMetrics->filter(fn($f) => $f->peer_avg !== null && $f->peer_avg < 2.50)->count();
        $distPending = $totalFaculty - $totalEvaluated;

        $barLabels = $facultyMetrics->pluck('short_name')->toArray();
        $barScores = $facultyMetrics->map(fn($f) => $f->peer_avg ?? 0)->toArray();
        $distributionValues = [$distOutstanding, $distVerySat, $distSat, $distNeedsImp, $distPending];

        return view('admin.evaluations.results', compact(
            'facultyMetrics', 'totalFaculty', 'totalEvaluated', 'completionRate',
            'overallInstMean', 'highestScore', 'lowestScore', 'totalSubmissions',
            'barLabels', 'barScores', 'distributionValues', 'search'
        ));
    }
}