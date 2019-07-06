<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\Category;
use Illuminate\Http\UploadedFile;

class ProductsTableSeeder extends Seeder
{
    private $allFakerPhotos;
    private $fakerPhotosPath = 'app/faker/product_photos';

    public function run()
    {

        $categories = Category::all();

        $this->allFakerPhotos = $this->getFakerPhotos();
        $this->deleteAllPhotosInProductsPath();
        $self = $this;
        factory(Product::class, 30)
            ->make()
            ->each(function (Product $product) use ($categories) {
                $product = Product::createWithPhoto($product->toArray() + [
                    'photo' => $this->getUploadedFile()
                ]);
                $categoryId = $categories->random()->id;
                $product->categories()->attach($categoryId);
            });
    }


    // delete todas as photos que foram enviadas no 'upload'
    private function deleteAllPhotosInProductsPath()
    {
        $path = Product::PRODUCTS_PATH;
        \File::deleteDirectory(storage_path($path), true);
    }

    //pega todas as fotos do diretório
    public function getFakerPhotos()
    {
        $path = storage_path($this->fakerPhotosPath);
        return collect(\File::allFiles($path));
    }

    private function getUploadedFile(){
        /** @var SplFileInfo $photoFile */
        $photoFile = $this->allFakerPhotos->random();
        $uploadFile = new \Illuminate\Http\UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        return $uploadFile;
    }

}
