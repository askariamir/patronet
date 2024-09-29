<?php

namespace App\Http\Controllers;

use App\Repositories\AttributeRepositoryInterface;
use App\Repositories\AttributeValueRepositoryInterface;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $brandRepository;
    protected $attributeRepository;
    protected $productRepository;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        AttributeRepositoryInterface $attributeRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->brandRepository = $brandRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
    }
    public function index()
    {

        $products = $this->productRepository->getAll();


        $products = $products->map(function ($product) {
            $product->attributes = $product->getAttributeDefinitions();
            return $product;
        });

        return response()->json([
            'products' => $products
        ]);
    }

    public function show($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }


        $attributeDefinitions = $product->getAttributeDefinitions();

        return response()->json([
            'product' => $product,
            'attributes' => $attributeDefinitions
        ]);
    }


    public function search(Request $request)
    {
        $brandName = $request->input('brand_name');
        $productCode = $request->input('product_code');
        $products = $this->productRepository->search($brandName, $productCode);

        return response()->json([
            'products' => $products
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $deleted = $this->productRepository->delete($id);

            if (!$deleted) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            DB::commit();
            return response()->json(['message' => 'Product deleted successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error deleting product'], 500);
        }
    }




}
