<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistrationStep;
use App\Models\RegistrationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationStepController extends Controller
{
    public function index()
    {
        $pageTitle = 'Registration Steps Management';
        $steps = RegistrationStep::with('questions')->ordered()->get();
        
        return view('admin.registration.steps.index', compact('pageTitle', 'steps'));
    }

    public function create()
    {
        $pageTitle = 'Create Registration Step';
        return view('admin.registration.steps.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_required' => 'boolean'
        ]);

        $step = RegistrationStep::create($request->all());

        $toast[] = ['success', 'Registration step created successfully'];
        return redirect()->route('admin.registration.steps.index')->withToasts($toast);
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Registration Step';
        $step = RegistrationStep::with('questions')->findOrFail($id);
        
        return view('admin.registration.steps.edit', compact('pageTitle', 'step'));
    }

    public function update(Request $request, $id)
    {
        $step = RegistrationStep::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'step_order' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_required' => 'boolean'
        ]);

        $step->update($request->all());

        $toast[] = ['success', 'Registration step updated successfully'];
        return redirect()->route('admin.registration.steps.index')->withToasts($toast);
    }

    public function destroy($id)
    {
        $step = RegistrationStep::findOrFail($id);
        
        // Delete associated questions first
        $step->questions()->delete();
        $step->delete();

        $toast[] = ['success', 'Registration step deleted successfully'];
        return redirect()->route('admin.registration.steps.index')->withToasts($toast);
    }

    public function toggleStatus($id)
    {
        $step = RegistrationStep::findOrFail($id);
        $step->is_active = !$step->is_active;
        $step->save();

        $status = $step->is_active ? 'activated' : 'deactivated';
        $toast[] = ['success', "Registration step {$status} successfully"];
        
        return response()->json([
            'success' => true,
            'message' => "Registration step {$status} successfully",
            'is_active' => $step->is_active
        ]);
    }

    // Question Management Methods
    public function addQuestion(Request $request, $stepId)
    {
        $step = RegistrationStep::findOrFail($stepId);
        
        $request->validate([
            'field_name' => 'required|string|max:255|unique:registration_questions,field_name',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,email,select,textarea,tel,password,checkbox',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'order' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'options.*.value' => 'required_with:options|string|max:255',
            'options.*.label' => 'required_with:options|string|max:255'
        ]);

        $questionData = $request->all();
        $questionData['step_id'] = $stepId;
        
        // Only include options if type is select
        if ($request->type !== 'select') {
            $questionData['options'] = null;
        }

        RegistrationQuestion::create($questionData);

        $toast[] = ['success', 'Question added successfully'];
        return redirect()->route('admin.registration.steps.edit', $stepId)->withToasts($toast);
    }

    public function updateQuestion(Request $request, $stepId, $questionId)
    {
        $question = RegistrationQuestion::where('step_id', $stepId)->findOrFail($questionId);
        
        $request->validate([
            'field_name' => 'required|string|max:255|unique:registration_questions,field_name,' . $questionId,
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,email,select,textarea,tel,password,checkbox',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'is_required' => 'boolean',
            'order' => 'required|integer|min:1',
            'options' => 'nullable|array',
            'options.*.value' => 'required_with:options|string|max:255',
            'options.*.label' => 'required_with:options|string|max:255'
        ]);

        $questionData = $request->all();
        
        // Only include options if type is select
        if ($request->type !== 'select') {
            $questionData['options'] = null;
        }

        $question->update($questionData);

        $toast[] = ['success', 'Question updated successfully'];
        return redirect()->route('admin.registration.steps.edit', $stepId)->withToasts($toast);
    }

    public function deleteQuestion($stepId, $questionId)
    {
        $question = RegistrationQuestion::where('step_id', $stepId)->findOrFail($questionId);
        $question->delete();

        $toast[] = ['success', 'Question deleted successfully'];
        return redirect()->route('admin.registration.steps.edit', $stepId)->withToasts($toast);
    }

    public function toggleQuestionStatus($stepId, $questionId)
    {
        $question = RegistrationQuestion::where('step_id', $stepId)->findOrFail($questionId);
        $question->is_active = !$question->is_active;
        $question->save();

        $status = $question->is_active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "Question {$status} successfully",
            'is_active' => $question->is_active
        ]);
    }

    public function reorderSteps(Request $request)
    {
        $request->validate([
            'steps' => 'required|array',
            'steps.*.id' => 'required|exists:registration_steps,id',
            'steps.*.step_order' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->steps as $stepData) {
                RegistrationStep::where('id', $stepData['id'])
                    ->update(['step_order' => $stepData['step_order']]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Steps reordered successfully'
        ]);
    }

    public function reorderQuestions(Request $request, $stepId)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:registration_questions,id',
            'questions.*.order' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request, $stepId) {
            foreach ($request->questions as $questionData) {
                RegistrationQuestion::where('id', $questionData['id'])
                    ->where('step_id', $stepId)
                    ->update(['order' => $questionData['order']]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Questions reordered successfully'
        ]);
    }
}
