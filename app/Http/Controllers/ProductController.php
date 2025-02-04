<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    private $typeMap = [
        1 => 'Food & Beverage',
        2 => 'Pharmaceuticals',
        3 => 'Government',
        4 => 'Traditional Medicine & Supplement',
        13 => 'Beauty, Cosmetics & Personal Care',
        14 => 'Media RTU',
        15 => 'K3L Products',
        16 => 'ALKES & PKRT',
        17 => 'Feed, Pesticides & PSAT',
        18 => 'Others',
        19 => 'Research / Academic Purpose',
        20 => 'Dioxine Udara'
    ];

    public function index()
    {
        return view('products', [
            'types' => $this->typeMap
        ]);
    }

    public function fetchProducts(Request $request)
    {
        $email = urlencode($request->email ?? 'youremail@example.com');
        $response = Http::get("https://bsby.siglab.co.id/api/test-programmer?email={$email}");
    
        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch products'], 500);
        }
    
        $responseData = $response->json();
        $products = $responseData['results'] ?? [];
    
        if ($request->filled('type')) {
            $products = array_values(array_filter($products, function ($product) use ($request) {
                return isset($product['type']) && $product['type'] == $request->type;
            }));
        }
    
        if ($request->filled('status')) {
            $products = array_values(array_filter($products, function ($product) use ($request) {
                return isset($product['status']) && strtolower($product['status']) == strtolower($request->status);
            }));
        }
    
        if ($request->filled('attachment')) {
            $products = array_values(array_filter($products, function ($product) use ($request) {
                return isset($product['attachment']) && 
                       (($request->attachment === 'yes' && !empty($product['attachment'])) ||
                        ($request->attachment === 'no' && empty($product['attachment'])));
            }));
        }
    
        if ($request->filled('discount')) {
            $products = array_values(array_filter($products, function ($product) use ($request) {
                return isset($product['discount']) &&
                       (($request->discount === 'yes' && $product['discount'] > 0) ||
                        ($request->discount === 'no' && $product['discount'] == 0));
            }));
        }
    
        return response()->json($products);
    }
}