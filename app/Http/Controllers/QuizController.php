<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Participant;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    private const ROUNDS = ['qualification', 'semifinal', 'final'];

    public function participants(): JsonResponse
    {
        $participants = Participant::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->limit(5)
            ->get(['id', 'name', 'display_order'])
            ->map(fn (Participant $participant): array => [
                'id' => $participant->id,
                'name' => $participant->name,
                'display_order' => $participant->display_order,
            ]);

        return response()->json([
            'data' => $participants,
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $round = $this->resolveRound($request);

        $categories = Category::query()
            ->withCount([
                'questions as questions_count' => fn ($query) => $query->where('round', $round),
            ])
            ->with([
                'questions' => fn ($query) => $query
                    ->select('id', 'category_id', 'round', 'number')
                    ->where('round', $round)
                    ->orderBy('number'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'round' => $round,
                'question_count' => $category->questions_count,
                'available_numbers' => $category->questions->pluck('number')->values(),
            ]);

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function category(Request $request, Category $category): JsonResponse
    {
        $round = $this->resolveRound($request);

        $category->load([
            'questions' => fn ($query) => $query
                ->select('id', 'category_id', 'round', 'number')
                ->where('round', $round)
                ->orderBy('number'),
        ]);

        return response()->json([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'round' => $round,
                'available_numbers' => $category->questions->pluck('number')->values(),
            ],
        ]);
    }

    public function question(Request $request, Category $category, int $number): JsonResponse
    {
        $round = $this->resolveRound($request);

        $question = Question::query()
            ->whereBelongsTo($category)
            ->where('round', $round)
            ->where('number', $number)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'category' => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'round' => $question->round,
                'difficulty' => $question->difficulty,
                'number' => $question->number,
                'prompt' => $question->prompt,
                'options' => [
                    'a' => $question->option_a,
                    'b' => $question->option_b,
                    'c' => $question->option_c,
                    'd' => $question->option_d,
                ],
                'correct_option' => $question->correct_option,
                'explanation' => $question->explanation,
            ],
        ]);
    }

    private function resolveRound(Request $request): string
    {
        $round = $request->query('round', 'qualification');

        if (! in_array($round, self::ROUNDS, true)) {
            abort(404);
        }

        return $round;
    }
}
