<?php

namespace App\Http\Controllers;

use App\Holiday;
use App\State;
use App\District;
use App\Block;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Session;
use Auth;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $query = Holiday::with(['state', 'district', 'block']);

        if (!empty($state_id)) {
            $query->where(function ($q) use ($state_id) {
                $q->where('state_id', $state_id)
                ->orWhereNull('state_id');
            });
        }
        if (!empty($district_id)) {
            $query->where('district_id', $district_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('state_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('district', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $holidays = $query->orderBy('date', 'desc')->paginate(10);

        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        Session::put('user', Auth::user());
        $user = Session::get('user');
        $state_id = $user->state_id;
        $district_id = $user->district_id;

        $states = State::all();
        $districts = District::where('state_id', $state_id)->get();
        $blocks = Block::whereIn('district_id', $districts->pluck('id'))->get();

        return view('admin.holidays.create', compact('states', 'districts', 'blocks'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:national,regional,optional',
            'applies_to' => 'required|in:all,state,district,block',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'is_recurring' => 'boolean',
            'duration' => 'required|in:full,half',
            'substitute_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                            ->withErrors($validator)
                            ->withInput();
        }

        Session::put('user', Auth::user());
        $user = Session::get('user');
        if ($request->applies_to === 'state') {
            $request->merge([
                'state_id' => $user->state_id,
                'district_id' => null,
                'block_id' => null,
            ]);
        }

        if ($request->applies_to === 'district') {
            $request->merge([
                'state_id' => $user->state_id,
                'block_id' => null,
            ]);
        }

        if ($request->applies_to === 'block') {
            $request->merge([
                'state_id' => $user->state_id,
            ]);
        }

        if ($request->applies_to === 'all') {
            $request->merge([
                'state_id' => null,
                'district_id' => null,
                'block_id' => null,
            ]);
        }
     Holiday::create($request->all());

        return redirect()->route('admin.holidays.index')
                        ->with('success', 'Holiday created successfully.');
    }

    public function edit($id)
    {
        $holiday = Holiday::with(['state', 'district', 'block'])->findOrFail($id);
        Session::put('user', Auth::user());
        $user = Session::get('user');
         $states = State::all();
         $districts = $user->state_id
        ? District::where('state_id', $user->state_id)->get()
        : District::all();

        $blocks = $holiday->district_id
            ? Block::where('district_id', $holiday->district_id)->get()
            : Block::all();

        return view('admin.holidays.edit', compact('holiday', 'states', 'districts', 'blocks'));
    }

    public function update($id, Request $request)
    {
        $holiday = Holiday::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:national,regional,optional',
            'applies_to' => 'required|in:all,state,district,block',
            'state_id' => 'nullable|exists:states,id',
            'district_id' => 'nullable|exists:districts,id',
            'block_id' => 'nullable|exists:blocks,id',
            'is_recurring' => 'boolean',
            'duration' => 'required|in:full,half',
            'substitute_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        Session::put('user', Auth::user());
        $user = Session::get('user');
          if ($request->applies_to === 'state') {
            $request->merge([
                'state_id' => $user->state_id,
                'district_id' => null,
                'block_id' => null,
            ]);
        }

        if ($request->applies_to === 'district') {
            $request->merge([
                'state_id' => $user->state_id,
                'block_id' => null,
            ]);
        }

        if ($request->applies_to === 'block') {
            $request->merge([
                'state_id' => $user->state_id,
            ]);
        }

        if ($request->applies_to === 'all') {
            $request->merge([
                'state_id' => null,
                'district_id' => null,
                'block_id' => null,
            ]);
        }
        $holiday->update($request->all());

        return redirect()->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully.');
    }
    public function show($id)
    {
        $holiday = Holiday::with(['state', 'district', 'block'])->findOrFail($id);

        return view('admin.holidays.show', compact('holiday'));
    }

    public function destroy($id)
    {
        $deleted = Holiday::where('id', $id)->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Holiday deleted successfully.' : 'Failed to delete holiday.'
        ]);
    }

   
}
