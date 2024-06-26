<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Enums\Role;
use App\Helpers\LocalRegionHelper;
use App\Http\Requests\BusinessCreateRequest;
use App\Http\Requests\BusinessRequest;
use App\Models\Account;
use App\Models\Business;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $businesses = new Business();
        $vaccines = Vaccine::isAllow()->get();

        if ($request->addr_province !== null) {
            $businesses = $businesses->where('addr_province', $request->addr_province);
        }

        if ($request->addr_district !== null) {
            $businesses = $businesses->where('addr_district', $request->addr_district);
        }

        if ($request->addr_ward !== null) {
            $businesses = $businesses->where('addr_ward', $request->addr_ward);
        }

        $businesses = $businesses->orderBy('addr_province', 'ASC')
            ->paginate(config('parameters.DEFAULT_PAGINATING_NUMBER'));

        return view('business.index', [
            'businesses' => $businesses,
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
        return view('business.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BusinessCreateRequest $request)
    {
        $request->validated();

        DB::transaction(function () use ($request) {
            Account::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => Role::ROLE_BUSINESS,
            ]);

            $addrProvinceName = LocalRegionHelper::getProvinceName($request->addr_province);
            $addrDistrictName = LocalRegionHelper::getDistrictName(
                $request->addr_province,
                $request->addr_district
            );
            $addrWardName = LocalRegionHelper::getWardName(
                $request->addr_province,
                $request->addr_district,
                $request->addr_ward
            );

            $accountId = Account::where('email', $request->email)->first()->id;
            Business::create([
                'account_id' => $accountId,
                'name' => $request->name,
                'tax_id' => $request->tax_id,
                'addr_province' => $request->addr_province,
                'addr_province_name' => $addrProvinceName,
                'addr_district' => $request->addr_district,
                'addr_district_name' => $addrDistrictName,
                'addr_ward' => $request->addr_ward,
                'addr_ward_name' => $addrWardName,
                'address' => $request->address,
                'contact' => $request->contact,
            ]);
        });

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __(
                'message.success',
                [
                    'action' => __('btn.create', [
                        'object' => '',
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
        $business = Business::findOrFail($id);
        $account = $business->account()->select('email')->first();
        $vaccineLots = $business->vaccineLots()
            ->paginate(config('parameters.DEFAULT_PAGINATING_NUMBER'));

        return view('business.show', [
            'account' => $account,
            'business' => $business,
            'vaccineLots' => $vaccineLots,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $business = Business::findOrFail($id);

        return view('business.edit', [
            'account' => $business->account,
            'business' => $business,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BusinessRequest $request, $id)
    {
        $request->validated();

        $addrProvinceName = LocalRegionHelper::getProvinceName($request->addr_province);
        $addrDistrictName = LocalRegionHelper::getDistrictName(
            $request->addr_province,
            $request->addr_district
        );
        $addrWardName = LocalRegionHelper::getWardName(
            $request->addr_province,
            $request->addr_district,
            $request->addr_ward
        );

        $business = Business::findOrFail($id);
        $business->tax_id = $request->tax_id;
        $business->name = $request->name;
        $business->addr_province = $request->addr_province;
        $business->addr_province_name = $addrProvinceName;
        $business->addr_district = $request->addr_district;
        $business->addr_district_name = $addrDistrictName;
        $business->addr_ward = $request->addr_ward;
        $business->addr_ward_name = $addrWardName;
        $business->address = $request->address;
        $business->contact = $request->contact;

        $business->save();

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __(
                'message.success',
                [
                    'action' => __('btn.update', [
                        'object' => '',
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
        Business::destroy($id);

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __('message.success', ['action' => __('btn.delete')]),
        ]);
    }

    public function trashed(Request $request)
    {
        $businesses = Business::onlyTrashed();
        $vaccines = Vaccine::isAllow()->get();

        if ($request->addr_province !== null) {
            $businesses = $businesses->where('addr_province', $request->addr_province);
        }

        if ($request->addr_district !== null) {
            $businesses = $businesses->where('addr_district', $request->addr_district);
        }

        if ($request->addr_ward !== null) {
            $businesses = $businesses->where('addr_ward', $request->addr_ward);
        }

        $businesses = $businesses->orderBy('addr_province', 'ASC')
            ->paginate(config('parameters.DEFAULT_PAGINATING_NUMBER'));

        return view('business.trashed', [
            'businesses' => $businesses,
            'vaccines' => $vaccines,
            'attributes' => $request,
        ]);
    }

    public function restore($id)
    {
        Business::withTrashed()->findOrFail($id)->restore();

        return redirect()->back()->with([
            'status' => ActionStatus::SUCCESS,
            'message' => __('message.success', ['action' => __('btn.restore')]),
        ]);
    }

    public function profile()
    {
        $business = Business::findOrFail(Auth::user()->business->id);

        return view('business.edit', [
            'account' => $business->account,
            'business' => $business,
        ]);
    }
}
