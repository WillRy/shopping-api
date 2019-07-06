<?php

use Illuminate\Database\Seeder;
use CodeShopping\Models\Product;
use CodeShopping\Models\ProductPhoto;
use Illuminate\Http\UploadedFile;

class ProductPhotosTableSeeder extends Seeder
{

    private $allFakerPhotos;
    private $fakerPhotosPath = 'app/faker/product_photos';

    public function run()
    {
        $this->allFakerPhotos = $this->getFakerPhotos();

        $product = Product::all();

        // removido exclusão de arquivos, pois seeder ProductsTableSeeder, irá fazer anteriormente
        // $this->deleteAllPhotosInProductsPath();
        $self = $this;
        $product->each(function ($product) use ($self) {
            $self->createPhotoDir($product);
            $self->createPhotosModels($product);
        });
    }

    //pega todas as fotos do diretório
    public function getFakerPhotos()
    {
        $path = storage_path($this->fakerPhotosPath);
        return collect(\File::allFiles($path));
    }

    // delete todas as photos que foram enviadas no 'upload'
    private function deleteAllPhotosInProductsPath()
    {
        $path = ProductPhoto::PRODUCTS_PATH;
        \File::deleteDirectory(storage_path($path), true);
    }

    // cria o diretório para as fotos
    private function createPhotoDir(Product $product)
    {
        $path = ProductPhoto::photosPath($product->id);
        \File::makeDirectory($path, 0777, true);
    }

    // chama 5 vezes o metodo que cria no banco o upload das fotos
    private function createPhotosModels(Product $product)
    {
        foreach (range(1, 5) as $v) {
            $this->createPhotoModel($product);
        }
    }

    // cria o registro de fotos no banco de dados
    private function createPhotoModel(Product $product)
    {
        $photo = ProductPhoto::create([
            'product_id' => $product->id,
            'file_name' => 'imagem.jpeg'
        ]);
        $this->generatePhoto($photo);
    }

    // registra o upload de foto com ela nomeada
    private function generatePhoto(ProductPhoto $photo)
    {
        $photo->file_name = $this->uploadPhoto($photo->product_id);
        $photo->save();
    }

    // faz o upload da foto e renomeia para um nome aleatorio
    private function uploadPhoto($productId)
    {
        $photoFile = $this->allFakerPhotos->random();
        $uploadFile = new UploadedFile(
            $photoFile->getRealPath(),
            str_random(16) . '.' . $photoFile->getExtension()
        );
        ProductPhoto::uploadFiles($productId,[$uploadFile]);
        return $uploadFile->hashName();
    }
}
