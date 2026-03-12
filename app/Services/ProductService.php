<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        private readonly SkuGeneratorService $skuGeneratorService,
        private readonly BarcodeService $barcodeService,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Product
    {
        $data['sku'] ??= $this->generateSku((string) $data['name']);
        $data['barcode'] ??= $this->barcodeService->make((string) now()->format('Uu'));

        /** @var Product $product */
        $product = Product::create(Arr::except($data, ['image']));

        $this->storeImageIfProvided($product, $data);

        return $product;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Product $product, array $data): Product
    {
        $product->fill(Arr::except($data, ['image']));
        if (empty($product->sku)) {
            $product->sku = $this->generateSku($product->name);
        }
        if (empty($product->barcode)) {
            $product->barcode = $this->barcodeService->make((string) now()->format('Uu'));
        }
        $product->save();

        $this->storeImageIfProvided($product, $data);

        return $product->refresh();
    }

    public function generateSku(string $name): string
    {
        return $this->skuGeneratorService->generate($name);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function storeImageIfProvided(Product $product, array $data): void
    {
        if (! isset($data['image']) || ! $data['image'] instanceof UploadedFile) {
            return;
        }

        $path = $data['image']->store('products', 'public');
        $product->images()->create([
            'path' => Storage::url($path),
            'is_primary' => true,
        ]);
    }
}
