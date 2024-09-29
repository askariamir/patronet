<?php

namespace App\Http\Controllers;

use App\Repositories\AttributeValueRepositoryInterface;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\AttributeRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected $brandRepository;
    protected $attributeRepository;
    protected $productRepository;
    protected $attributeValueRepository;

    public function __construct(
        BrandRepositoryInterface $brandRepository,
        AttributeRepositoryInterface $attributeRepository,
        AttributeValueRepositoryInterface $attributeValueRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->brandRepository = $brandRepository;
        $this->attributeValueRepository = $attributeValueRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
    }
    public function index()
    {
        $brands = $this->brandRepository->getAll();  // Fetch all brands

        return response()->json([
            'brands' => $brands
        ]);
    }
    public function show($id)
    {
        $brand = $this->brandRepository->find($id);  // Fetch brand by ID

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        return response()->json([
            'brand' => $brand
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand.name' => 'required|string|max:255',
            'brand.attributes' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $brand = $this->brandRepository->create([
                'name' => $validated['brand']['name']
            ]);

            $attributes = $validated['brand']['attributes'];
            $attributeIds = [];

            foreach ($attributes as $attrName => $values) {

                $attribute = $this->attributeRepository->create([
                    'name' => $attrName,
                    'brand_id' => $brand->id
                ]);

                foreach ($values as $value) {

                    $this->attributeValueRepository->create([
                        'attribute_id' => $attribute->id,
                        'value' => $value['value'],
                        'description' => $value['description']
                    ]);
                }


                $attributeIds[$attrName] = array_column($values, 'value');
            }

            $combinations = $this->generateCombinations($attributeIds);

            foreach ($combinations as $combination) {

                $product_code = implode('-', array_values($combination));


                $this->productRepository->create([
                    'brand_id' => $brand->id,
                    'product_code' => $product_code,
                    'combination' => json_encode($combination)
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Brand and products created successfully!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while creating the brand.'], 500);
        }
    }
    public function update($id, Request $request)
    {
        DB::beginTransaction();

        try {

            $brand = $this->brandRepository->update($id, ['name' => $request->input('brand.name')]);

            if (!$brand) {
                return response()->json(['error' => 'Brand not found'], 404);
            }


            $this->attributeRepository->deleteByBrandId($id);


            $this->productRepository->deleteByBrandId($id);


            $attributes = $request->input('brand.attributes');
            $attributeIds = [];

            foreach ($attributes as $attrName => $values) {

                $attribute = $this->attributeRepository->create([
                    'name' => $attrName,
                    'brand_id' => $brand->id
                ]);

                foreach ($values as $value) {

                    $this->attributeValueRepository->create([
                        'attribute_id' => $attribute->id,
                        'value' => $value['value'],
                        'description' => $value['description']
                    ]);
                }

                $attributeIds[$attrName] = array_column($values, 'value');
            }


            $combinations = $this->generateCombinations($attributeIds);

            foreach ($combinations as $combination) {
                $productCode = implode('-', array_values($combination));

                $this->productRepository->create([
                    'brand_id' => $brand->id,
                    'product_code' => $productCode,
                    'combination' => json_encode($combination)
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Brand and products updated successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error updating brand'], 500);
        }
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $deleted = $this->brandRepository->delete($id);

            if (!$deleted) {
                return response()->json(['error' => 'Brand not found'], 404);
            }

            DB::commit();
            return response()->json(['message' => 'Brand and its related data deleted successfully!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error deleting brand'], 500);
        }
    }
    private function generateCombinations($attributes)
    {
        $result = [[]];

        foreach ($attributes as $key => $values) {
            $temp = [];

            foreach ($result as $product) {
                foreach ($values as $value) {
                    $temp[] = array_merge($product, [$key => $value]);
                }
            }

            $result = $temp;
        }

        return $result;
    }
}

