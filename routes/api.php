<?php

use App\Http\Controllers\API\AuthenticationApiController;
use App\Http\Controllers\API\BlogApiController;
use App\Http\Controllers\API\CartApiController;
use App\Http\Controllers\API\CategoryApiController;
use App\Http\Controllers\API\CustomerApiController;
use App\Http\Controllers\API\FileApiController;
use App\Http\Controllers\API\ImageAssignApiController;
use App\Http\Controllers\API\InvoiceApiController;
use App\Http\Controllers\API\InvoiceDetailApiController;
use App\Http\Controllers\API\ProductApiController;
use App\Http\Controllers\API\ProductDetailApiController;
use App\Http\Controllers\API\ProviderApiController;
use App\Http\Controllers\API\WebInforApiController;
use App\Http\Resources\BlobResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Blob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('admin')->group(function () {
    Route::get('products/{id}', [ProductApiController::class, 'show']);
    Route::get('blobs/{id}', function ($id) {
        return Blob::find($id);
    });
});
Route::middleware(['admin', 'auth:api'])->prefix('admin')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => new UserResource(Auth::user()),
            'meta' => []
        ]);
    });
    Route::resource('products', ProductApiController::class)->except(['edit', 'create']);
    Route::resource('blogs', BlogApiController::class)->except(['edit', 'create'])->withoutMiddleware(['admin', 'auth:api']);
    Route::resource('product-details', ProductDetailApiController::class)->except(['edit', 'create'])->withoutMiddleware(['admin', 'auth:api']);
    Route::resource('invoices', InvoiceApiController::class)->except(['edit', 'create'])->withoutMiddleware(['admin', 'auth:api']);
    Route::resource('invoice-details', InvoiceDetailApiController::class)->except(['edit', 'create'])->withoutMiddleware(['admin', 'auth:api']);
    Route::resource('carts', CartApiController::class)->except(['edit', 'create'])->withoutMiddleware(['admin']);
    Route::resource('categories', CategoryApiController::class)->except(['edit', 'create']);
    Route::resource('providers', ProviderApiController::class)->except(['edit', 'create']);
    Route::resource('webinfos', WebInforApiController::class)->except(['edit', 'create']);
    Route::resource('customers', CustomerApiController::class)->except(['edit', 'create']);
    Route::post('carts/range', [CartApiController::class, 'storeRange'])->withoutMiddleware(['admin']);
    Route::post('carts/checkout', [CartApiController::class, 'checkOut'])->name('carts.checkout')->withoutMiddleware(['admin']);
    Route::get('products', [ProductApiController::class, 'index'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('webinfos', [WebInforApiController::class, 'index'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('categories', [CategoryApiController::class, 'index'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('blogs', [BlogApiController::class, 'index'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('blogs/{id}', [BlogApiController::class, 'show'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('invoices/{id}', [InvoiceApiController::class, 'show'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('providers', [ProviderApiController::class, 'index'])->withoutMiddleware(['admin', 'auth:api']);
    Route::get('blobs', [FileApiController::class, 'getListBlob'])->name('file.index')->withoutMiddleware(['admin', 'auth:api']);
    Route::post('logout', [AuthenticationApiController::class, 'logout'])->name('auth.logout');
    Route::post('image_assigns', [ImageAssignApiController::class, 'store'])->name('image_assign.store');
    Route::post('upload', [FileApiController::class, 'uploadRange'])->name('file.uploadRange')->withoutMiddleware(['admin', 'auth:api']);
    Route::post('file/duplicated-filter', [FileApiController::class, 'duplicatedFilter'])->name('file.duplicatedFilter')->withoutMiddleware(['admin', 'auth:api']);
    Route::post('blobs/duplicate/{id}', [FileApiController::class, 'duplicateBlob'])->name('file.duplicateBlob')->withoutMiddleware(['admin', 'auth:api']);
    Route::delete('image_assigns/{id}', [ImageAssignApiController::class, 'destroy'])->name('image_assign.delete');
});

Route::middleware(['auth:api'])->group(function () {
    Route::post(
        'admin/invoices/create',
        [InvoiceApiController::class, "store"]
    );
    Route::get('/user', function (Request $request) {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $userResponse = new UserResource($user);
        $userResponse->customer = new CustomerResource($user->customer);
        $userResponse->customer->image = new BlobResource($user->customer->image);
        return response()->json([
            'code' => Response::HTTP_OK,
            'status' => true,
            'data' => $userResponse,
            'meta' => []
        ]);
    });
    Route::put('/user', [CustomerApiController::class, 'updateOne']);
    Route::post('login', [AuthenticationApiController::class, 'login'])->withoutMiddleware(['auth:api'])->name('auth.login');
    Route::post('logout', [AuthenticationApiController::class, 'logout'])->name('auth.logout');
    Route::resource('carts', CartApiController::class)->except(['edit', 'create']);
});

Route::post('register', [AuthenticationApiController::class, 'register'])->name('auth.register');
Route::post('blobs/{id}', [FileApiController::class, 'updateBlob'])->name('blob.update');
Route::post('upload', [FileApiController::class, 'upload'])->name('file.upload');
Route::delete('blobs/{id}', [FileApiController::class, 'delete'])->name('blob.delete')->middleware(['auth:api']);
Route::get('files/{name}', [FileApiController::class, 'get'])->name('file.get');
Route::get('blobs/{id}', [FileApiController::class, 'getByBlob'])->name('file.blob');
Route::get('blobs/download/{id}', [FileApiController::class, 'downloadById'])->name('file.blob');
Route::get('files/download/{name}', [FileApiController::class, 'download'])->name('file.download');
Route::get('product', [ProductApiController::class, 'index'])->name('home.product');
Route::get('product/{id}', [ProductApiController::class, 'show'])->name('home.product.show');

Route::prefix('admin')->group(function () {
    Route::post('login', [AuthenticationApiController::class, 'adminLogin'])->name('auth.admin.login');
});

// Route::get('/products', [ProductApiController::class, 'Index']);
