<?php

namespace App\Http\Controllers;

use App\Repositories\AttributeValueRepositoryInterface;
use App\Repositories\BrandRepositoryInterface;
use App\Repositories\AttributeRepositoryInterface;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // Create the brand
            $brand = $this->brandRepository->create([
                'name' => $validated['brand']['name'],
            ]);

            // Create attributes and their values
            $attributeIds = $this->createAttributesAndValues($validated['brand']['attributes'], $brand->id);

            //  Generate combinations and create products
            $this->createProducts($brand->id, $attributeIds);

            DB::commit();

            return response()->json(['message' => 'Brand and products created successfully!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error while creating brand and products: ' . $e->getMessage());

            return response()->json(['error' => 'An error occurred while creating the brand.'], 500);
        }
    }
    private function createAttributesAndValues(array $attributes, $brandId)
    {
        $attributeIds = [];

        // ویژگی‌های مربوط به برند دیتابیس ذخیره می‌شوند
        foreach ($attributes as $attrName => $values) {
            $attribute = $this->attributeRepository->create([
                'name' => $attrName,
                'brand_id' => $brandId
            ]);

            //  مقادیر ویژگی اضافه می‌شوند
            $attributeValues = array_map(function($value) use ($attribute) {
                return [
                    'attribute_id' => $attribute->id,
                    'value' => $value['value'],
                    'description' => $value['description']
                ];
            }, $values);

            $this->attributeValueRepository->insert($attributeValues);

            $attributeIds[$attrName] = array_column($values, 'value');

        }
        return $attributeIds;
    }
    private function createProducts($brandId, array $attributeIds)
    {
        $combinations = $this->generateCombinations($attributeIds);
        $products = [];

        foreach ($combinations as $combination) {
            $productCode = implode('-', array_values($combination));
            $products[] = [
                'brand_id' => $brandId,
                'product_code' => $productCode,
                'combination' => json_encode($combination),
            ];
        }

        $this->productRepository->insert($products);
    }
    private function generateCombinations($attributes)
    {
        $result = [[]];
        // برای هر ویژگی یک لوپ ایجاد می‌شود
        foreach ($attributes as $key => $values) {
            // برای ذخیره ترکیب جدید
            $temp = [];

            foreach ($result as $product) {
            // چیزی که درحال حاضر از result داریم درواقع همون productمونه که کم کم تکمیل میشه
                foreach ($values as $value) {
                    $temp[] = array_merge($product, [$key => $value]);
                }
            }
            $result = $temp;
        }

        return $result;
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

}

