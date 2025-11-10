<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    public function __construct(
        private RajaOngkirService $rajaOngkirService
    ) {}

    public function getProvinces()
    {
        $provinces = $this->rajaOngkirService->getProvinces();

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'province_id' => ['nullable', 'string'],
        ]);

        $cities = $this->rajaOngkirService->getCities($request->province_id);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function checkCost(Request $request)
    {
        $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['required', 'string', 'in:jne,tiki,pos'],
        ]);

        $costs = $this->rajaOngkirService->checkCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier
        );

        return response()->json([
            'success' => true,
            'data' => $costs,
        ]);
    }

    public function getCityId(Request $request)
    {
        $request->validate([
            'city_name' => ['required', 'string'],
            'province_name' => ['nullable', 'string'],
        ]);

        $cityId = $this->rajaOngkirService->getCityId(
            $request->city_name,
            $request->province_name
        );

        return response()->json([
            'success' => true,
            'city_id' => $cityId,
        ]);
    }
}
