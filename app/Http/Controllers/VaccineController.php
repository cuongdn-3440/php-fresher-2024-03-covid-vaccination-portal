<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Http\Requests\VaccineRequest;
use App\Models\Vaccine;
use Illuminate\Http\Request;

class VaccineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $vaccines = new Vaccine();

        if ($request->is_allow !== null) {
            $vaccines = $vaccines->where('is_allow', $request->is_allow);
        }

        $vaccines = $vaccines->paginate(config('parameters.DEFAULT_PAGINATING_NUMBER'));

        return view('vaccine.index', [
            'vaccines' => $vaccines,
            'attributes' => $request,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vaccine.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VaccineRequest $request)
    {
        $request->validated();

        Vaccine::create([
            'name' => $request->name,
            'supplier' => $request->supplier,
            'technology' => $request->technology,
            'country' => $request->country,
            'is_allow' => $request->is_allow,
        ]);

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __(
                'message.success',
                [
                    'action' => __('btn.import', [
                        'object' => __('vaccine.vaccine'),
                    ]),
                ],
            ),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vaccine = Vaccine::findOrFail($id);

        return view('vaccine.edit', ['vaccine' => $vaccine]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VaccineRequest $request, $id)
    {
        $request->validated();

        $vaccine = Vaccine::findOrFail($id);
        $vaccine->name = $request->name;
        $vaccine->supplier = $request->supplier;
        $vaccine->technology = $request->technology;
        $vaccine->country = $request->country;
        $vaccine->is_allow = $request->is_allow;

        $vaccine->save();

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __(
                'message.success',
                [
                    'action' => __('btn.update', [
                        'object' => __('vaccine.vaccine'),
                    ]),
                ],
            ),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Vaccine::destroy($id);

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __('message.success', ['action' => __('btn.delete')]),
        ]);
    }
}
