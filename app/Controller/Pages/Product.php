<?php

namespace app\Controller\Pages;

use \app\Utils\View;
use \app\Model\Entity\Organization;
use \app\Model\Entity\Product as EntityProduct;
use \app\Db\Pagination;
use \app\Controller\Pages\Alert;

class Product extends Page
{
   private static function getProductItems($request, &$obPagination, $key = null)
   {
      $items  = '';
      $totalQuantity = EntityProduct::getProducts(null, null, null, 'count (*) as quantity')->fetchObject()->quantity;
      !is_null($key) ? $where = "id = " . $key : $where = '';
      $queryParams = $request->getQueryParams();


      $currentPage = $queryParams['page'] ?? 1;

      //INSTANCIA DE PAGINAÇÃO

      $obPagination = new Pagination($totalQuantity, $currentPage, 10); // 10 por pagina

      $results = EntityProduct::getProducts($where, 'id DESC', $obPagination->getLimit());
      while ($obProduct = $results->fetchObject(EntityProduct::class)) {
         $items .= View::render('pages/Product/Product_row', [
            'description' => $obProduct->description,
            'brand' => $obProduct->brand,
            'id' => $obProduct->id,
            'type' => $obProduct->type,
            'size' => $obProduct->size,
            'color' => $obProduct->color,
            'price' => $obProduct->price,
            'quantity' => $obProduct->quantity,
         ]);
      }
      return $items;
   }


   public static function getProducts($request, $errorMessage = null, $key = null)
   {

      $status = !is_null($errorMessage) ? Alert::getSuccess($errorMessage) : '';


      $content =  View::render('pages/Product/Products', [
         'items' => self::getProductItems($request, $obPagination, $key),
         'pagination' => parent::getPagination($request, $obPagination),
         'status' => $status,
         'search' => !is_null($key) ? $key : 'Procure por id '
      ]);

      return parent::getPage("Produtos", $content);
   }

   public static function getForm($request, $status, $title, $obProduct = null)
   {
      $content =  View::render('pages/Product/form', [
         'code' => $obProduct->id ?? '',
         'readonly' => is_null($obProduct) ? '' : 'readonly',
         'option' => $title,
         'status' => $status,
         'description' => $obProduct->description ?? '',
         'brand' => $obProduct->brand ?? '',
         'id' => $obProduct->id ?? '',
         'type' => $obProduct->type ?? '',
         'size' => $obProduct->size ?? '',
         'color' => $obProduct->color ?? '',
         'price' => $obProduct->price ?? '',
         'quantity' => $obProduct->quantity ?? '',
      ]);
      return parent::getPage($title, $content);
   }

   public static function addProduct($request, $errorMessage = null)
   {
      $status = !is_null($errorMessage) ?
         Alert::getError($errorMessage) : '';

      return self::getForm($request, $status, "Adicionar produto", null);
   }

   public static function validate($request, $obProduct)
   {
      if ($obProduct instanceof EntityProduct) {
         return self::getForm($request, 'Produto com código ' . $obProduct->id . ' já cadastrado', 'Adicionar produto', null);
      }
      $obProduct->save();
      return self::getProducts($request, "Produto cadastrado com sucesso!");
   }




   public static function insertProduct($request)
   {
      $postVars = $request->getPostVars();
      $obProduct = new EntityProduct;
      $obProduct->description = $postVars['description'];
      $obProduct->id = $postVars['code'];
      $obProduct->brand = $postVars['brand'];
      $obProduct->type = $postVars['type'];
      $obProduct->size = $postVars['size'];
      $obProduct->color = $postVars['color'] ?? '';
      $obProduct->price = $postVars['price'] ?? '';
      $obProduct->quantity = $postVars['quantity'];



      return self::validate($request, $obProduct);
   }


   public static function setEditProduct($request, $id, $errorMessage = null)
   {

      $status = !is_null($errorMessage) ?
         Alert::getSuccess($errorMessage) : '';

      $obProduct = EntityProduct::getProductById($id);


      if (!$obProduct instanceof EntityProduct) {
         return self::getForm($request, 'Código não encontrado', 'title');
      }

      $postVars = $request->getPostVars();
      $obProduct = new EntityProduct;
      $obProduct->id = $id;
      $obProduct->description = $postVars['description'] ?? $obProduct->description;
      $obProduct->brand = $postVars['brand'] ?? $obProduct->brand;
      $obProduct->type = $postVars['type'] ?? $obProduct->type;
      $obProduct->size = $postVars['size'] ?? $obProduct->size;
      $obProduct->color = $postVars['color'] ??  $obProduct->color;
      $obProduct->price = $postVars['price'] ??  $obProduct->price;
      $obProduct->quantity = $postVars['quantity'] ??  $obProduct->quantity;

      if (!$obProduct->update() instanceof EntityProduct) {
         return self::getProducts($request, "Produto atualizado com sucesso!");
      } else {
         $status = Alert::getError("Não foi possível editar!");
         return self::getForm($request, $status, "Editar produto", $obProduct);
      }
   }


   public static function getEditProduct($request, $id, $errorMessage = null)
   {

      $status = !is_null($errorMessage) ?
         Alert::getSuccess($errorMessage) : '';

      $obProduct = EntityProduct::getProductById($id);

      if (!$obProduct instanceof EntityProduct) {
         $request->getRouter()->redirect('/pages/Product/Products');
      }

      return self::getForm($request, $status, "Editar cliente", $obProduct);
   }

   public static function searchProducts($request)
   {
      $key = $request->getPostVars()['search_name'];
      return self::getProducts($request, null, $key);
   }

   public static function getDeleteProduct($request, $id)
   {
      $obProduct = EntityProduct::getProductById($id);


      if (!$obProduct instanceof EntityProduct) {
         $request->getRouter()->redirect('/pages/Product/Products');
      }

      if ($obProduct->delete($id)) {
         return self::getProducts($request, "Cliente deletado com sucesso!");
      }
   }
}
