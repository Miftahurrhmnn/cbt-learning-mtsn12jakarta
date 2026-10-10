<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FullCalenderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $user = Auth::user();
            $query = Event::query();

            if ($request->filled('start') && $request->filled('end')) {
                $query->where(function ($q) use ($request) {
                    $q->whereBetween('start', [$request->start, $request->end])
                        ->orWhereBetween('end', [$request->start, $request->end])
                        ->orWhere(function ($sub) use ($request) {
                            $sub->whereDate('start', '<=', $request->end)
                                ->where(function ($endSub) use ($request) {
                                    $endSub->whereDate('end', '>=', $request->start)
                                        ->orWhereNull('end');
                                });
                        });
                });
            }

            if ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)->orWhereNull('user_id');
                });
            }

            $data = $query->get(['id', 'user_id', 'title', 'description', 'color', 'start', 'end']);

            $events = $data->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description ?? '',
                    'color' => $event->color ?: '#4f46e5',
                    'textColor' => '#ffffff',
                    'start' => $event->start,
                    'end' => $event->end,
                ];
            });

            return response()->json($events);
        }

        return view('guru.calendar.fullcalendar');
    }

    public function ajax(Request $request): JsonResponse
    {
        switch ($request->type) {
            case 'add':
                $event = Event::create([
                    'user_id' => Auth::id(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'color' => $request->color ?: '#4f46e5',
                    'start' => $request->start,
                    'end' => $request->end,
                ]);

                return response()->json([
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description ?? '',
                    'color' => $event->color ?: '#4f46e5',
                    'textColor' => '#ffffff',
                    'start' => $event->start,
                    'end' => $event->end,
                    'success' => true,
                ]);

            case 'update':
                $event = Event::findOrFail($request->id);
                $updateData = [];

                if ($request->filled('title')) {
                    $updateData['title'] = $request->title;
                }
                if ($request->has('description')) {
                    $updateData['description'] = $request->description;
                }
                if ($request->filled('color')) {
                    $updateData['color'] = $request->color;
                }
                if ($request->filled('start')) {
                    $updateData['start'] = $request->start;
                }
                if ($request->filled('end')) {
                    $updateData['end'] = $request->end;
                }

                $event->update($updateData);

                return response()->json([
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description ?? '',
                    'color' => $event->color ?: '#4f46e5',
                    'textColor' => '#ffffff',
                    'start' => $event->start,
                    'end' => $event->end,
                    'success' => true,
                ]);

            case 'delete':
                $event = Event::findOrFail($request->id);
                $event->delete();

                return response()->json(['success' => true]);

            default:
                return response()->json(['error' => 'Invalid action type'], 400);
        }
    }
}
