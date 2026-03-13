<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

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
        $data['sku'] = $this->normalizeOrGenerateSku($data['sku'] ?? null, (string) $data['name']);
        $data['barcode'] = $this->normalizeOrGenerateBarcode($data['barcode'] ?? null);
        $data['selling_price'] = $this->normalizeDecimal($data['selling_price'] ?? null);
        $data['cost_price'] = $this->normalizeDecimal($data['cost_price'] ?? null);

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
        $payload = Arr::except($data, ['image']);
        $payload['sku'] = $this->normalizeOrGenerateSku($payload['sku'] ?? null, (string) ($payload['name'] ?? $product->name));
        $payload['barcode'] = $this->normalizeOrGenerateBarcode($payload['barcode'] ?? null);
        $payload['selling_price'] = $this->normalizeDecimal($payload['selling_price'] ?? null);
        $payload['cost_price'] = $this->normalizeDecimal($payload['cost_price'] ?? null);

        $product->fill($payload);
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

        ProductImage::query()
            ->where('product_id', $product->id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        $product->images()->create([
            'path' => $path,
            'is_primary' => true,
        ]);
    }

    private function normalizeOrGenerateSku(mixed $sku, string $name): string
    {
        $value = trim((string) $sku);

        return $value !== '' ? $value : $this->generateSku($name);
    }

    private function normalizeOrGenerateBarcode(mixed $barcode): string
    {
        $value = trim((string) $barcode);

        return $value !== '' ? $value : $this->barcodeService->make((string) now()->format('Uu'));
    }

    private function normalizeDecimal(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        return max(0.0, (float) $value);
    }
}
