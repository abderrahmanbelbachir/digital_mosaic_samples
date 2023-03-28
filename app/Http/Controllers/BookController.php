<?php

namespace App\Http\Controllers;

use App\Mail\ProductCreated;
use App\Models\Edition;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)

    {
        $productName = '';
        $category = '';
        $products = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereNotNull('pictures')
            ->whereHas('store')
            ->with('store');
            if (isset($request->keyword)) {
                if($request->filterType == 'id'){
                    $products = $products->where(function($query) use ($request) {
                        $query->where('id', 'like', '%' . $request->keyword . '%');
                    });
                }
                else if($request->filterType == 'name'){
                    $products = $products->where(function($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->keyword . '%');
                    });
                }
                else if($request->filterType == 'author'){
                    $products = $products->where(function($query) use ($request) {
                        $query->where('author', 'like', '%' . $request->keyword . '%');
                    });
                }
                else if($request->filterType == 'maisonEdition'){
                    $products = $products->where(function($query) use ($request) {
                        $query->where('maisonEdition', 'like', '%' . $request->keyword . '%');
                    });
                }
                else if($request->filterType == 'category'){
                    $products = $products->where(function($query) use ($request) {
                        $query->where('category', 'like', '%' . $request->keyword . '%');
                    });
                }
                else{
                    $products = $products->where(function($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->keyword . '%')
                            ->orWhere('id', 'like', '%' . $request->keyword . '%')
                            ->orWhere('category', 'like', '%' . $request->keyword . '%')
                            ->orWhere('author', 'like', '%' . $request->keyword . '%')
                            ->orWhere('maisonEdition', 'like', '%' . $request->keyword . '%')
                            ->orWhere('subCategory', 'like', '%' . $request->keyword . '%')
                            ->orWhere('mark', 'like', '%' . $request->keyword . '%')
                            ->orWhere('wilaya', 'like', '%' . $request->keyword . '%')
                            ->orWhere('magasinId', 'like', '%' . $request->keyword . '%')
                            ->orWhereHas('store', function($query) use ($request) {
                                $query->where('title' , 'like' , '%'.$request->keyword.'%');
                            });
                    });
                }
            }
            if(isset($request->sortColumn)){
                if(isset($request->sortType)){
                    $products = $products->orderBy($request->sortColumn , $request->sortType);
                }
                else{
                    $products = $products->orderBy($request->sortColumn);
                }
            }
            else{
                $products = $products->orderBy('created_at' , 'desc');
            }
        if(isset($request->all)){
            return $products->get();
        }
        else{
            return $products->paginate(10);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)

    {
        $store = Store::where('id', $request->magasinId)->first();


        if ($store) {
            $inputs = $request->only(['name', 'category', 'description', 'subCategory',
                'price', 'stockDispo', 'mark', 'pictures', 'isPublished', 'quantityOnOrder',
                'reviews', 'ratingAverage', 'properties', 'discounts', 'homePlace', 'step', 'deliveredBy',
                'validatedAt', 'wilaya', 'magasinId' , 'type' , 'summary' , 'author' ,
                'language' , 'totalPages' , 'maisonEdition', 'isBook']);

            if (!isset($request->wilaya)) {
                $inputs['wilaya'] = $store->wilaya;
            }

            if (isset($inputs['pictures'])) {
                $inputs['pictures'] = json_encode($inputs['pictures']);
            }

            if (isset($inputs['subCategory'])) {
                $inputs['subCategory'] = json_encode($inputs['subCategory']);
            }
            if (isset($inputs['properties'])) {
                $inputs['properties'] = json_encode($inputs['properties']);
            }
            if (isset($inputs['reviews'])) {
                $inputs['reviews'] = json_encode($inputs['reviews']);
            }
            if (isset($inputs['discounts'])) {
                $inputs['discounts'] = json_encode($inputs['discounts']);
            }
            if ($store->isFreeDelivery && $store->isFreeDelivery == 1) {
                $inputs['isFreeDelivery'] = 1;
            } else {
                $inputs['isFreeDelivery'] = 0;
            }
            $inputs['isBook'] = 1;
            // return $inputs;
            $product = Product::where('isBook' , 1)->create($inputs);
            $productService = new ProductService();
            $productService->updateStockDispo($product);
            // Mail::to('Contact@placetta.com')->send(new ProductCreated($product , $store));
            // Mail::to('Samia.bouibed@placetta.com')->send(new ProductCreated($product , $store));
            return $product->id;
        }
        return ['store not found !!! ', $request->all()];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)

    {
        $product = Product::where('isBook' , 1)->where('id' , $id)->with(['store' => function ($query) {
            $query->select('id' , 'title' , 'picture' , 'delivery' , 'deliveryType', 'isFreeDelivery');
        }])->first();
        if ($product->pictures) {
            $product->pictures = json_decode($product->pictures);
        }

        if ($product->subCategory) {
            $product->subCategory = json_decode($product->subCategory);
        }
        if ($product->properties) {
            $product->properties = json_decode($product->properties);
        }
        if ($product->reviews) {
            $product->reviews = json_decode($product->reviews);
        }
        if ($product->discounts) {
            $product->discounts = json_decode($product->discounts);
        }
        return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)

    {
        $inputs = $request->only(['name', 'category', 'description', 'subCategory',
            'price', 'stockDispo', 'mark', 'pictures', 'isPublished', 'quantityOnOrder',
            'reviews', 'ratingAverage', 'properties', 'discounts', 'homePlace', 'step', 'deliveredBy',
            'validatedAt', 'wilaya', 'id' , 'type' , 'summary' , 'author' ,
            'language' , 'totalPages' , 'maisonEdition']);

        if (isset($inputs['pictures'])) {
            $inputs['pictures'] = json_encode($inputs['pictures']);
        }

        if (isset($inputs['subCategory'])) {
            $inputs['subCategory'] = json_encode($inputs['subCategory']);
        }
        if (isset($inputs['properties'])) {
            $inputs['properties'] = json_encode($inputs['properties']);
        }
        if (isset($inputs['reviews'])) {
            $inputs['reviews'] = json_encode($inputs['reviews']);
        }
        if (isset($inputs['discounts'])) {
            $inputs['discounts'] = json_encode($inputs['discounts']);
        }

        Product::where('isBook' , 1)->where('id', $id)->update($inputs);
        $productService = new ProductService();
        $productService->updateStockDispo(Product::where('isBook' , 1)->where('id', $id)->first());

        return 'product updated successfully !!';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)

    {
        $productOrdersInprogress = Order::where([['products', 'like', '"id":"' . $id . '"'], ['status', 'inProgress']])
            ->count();
        if ($productOrdersInprogress > 0) {
            return response()->json([
                'message' => 'products have an inprogress order', 'inProgressOrder' => true], 404);
        } else {
            Product::where('isBook' , 1)->where('id', $id)->delete();
        }

        return 'product destroyed successfully';
    }


    public function getBooksHome()
    {
        $products = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])
            ->inRandomOrder()
            ->limit(10)
            ->get();
        return $products;

    }

    public function getProductsForSearchBar(Request $request)
    {
        $products = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereNotNull('pictures')
            ->whereHas('store' , function ($query) {
                $query->whereNotNull('validatedAt');
            })
            ->with(['store' => function ($query) {
                $query->whereNotNull('validatedAt');
            }])
            ->orderBy('created_at', 'desc');

        if (isset($request->keyword)) {
            $products = $products->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
            });
        }

        return $products->paginate(3);
    }

    public function fixBookStore(){
        Product::where('isBook',1)->update(['magasinId'=>381]);
        return 'books fixed';
    }
    public function updateBooksWithoutPic(){
        Product::where('isBook',1)->
        where('pictures', '[]')->update(['pictures'=>['https://firebasestorage.googleapis.com/v0/b/placetta-mobile.appspot.com/o/books%2Fbook-placeholder.png?alt=media&token=8cc2c97e-1cc1-4414-9daf-341f7f181ccf']]);
        return 'books fixed';
    }
    public function searchBooks(Request $request) {
        $productName = '';
        $category = '';
        $products = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereNotNull('pictures')
            ->whereHas('store')
            ->with('store');

        if ($request->productName) {
            $products = $products->where('name', 'like', '%' . $request->productName . '%');
        }
        if ($request->category) {
            $products = $products->where('category', 'like', '%' . $request->category . '%');
        }
        if ($request->minPrice && $request->maxPrice) {
            $products = $products->whereBetween('price', [intval($request->minPrice), intval($request->maxPrice)]);
        }

        if ($request->maisonEdition) {
            $products = $products->where('maisonEdition', 'like', '%' . $request->maisonEdition . '%');
        }

        if ($request->author) {
            $products = $products->where('author', 'like', '%' . $request->author . '%');
        }

        if ($request->keyword) {
            $products = $products->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
            });
        }

        $products = $products->orderBy('created_at' , 'desc');

        return $products->paginate(10);
    }

    public function createbooks() {

        // dammah books
        $books = [
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-أبي-الجبل-scaled-500x783.jpg"
                ],
                "price" => 600,
                "name" => "أبي الجبل",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a3%d8%a8%d9%8a-%d8%a7%d9%84%d8%ac%d8%a8%d9%84/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-رواية-رعاة-أركاديا-scaled-500x814.jpg"
                ],
                "price" => 700,
                "name" => "رعاة أركاديا",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%b9%d8%a7%d8%a9-%d8%a3%d8%b1%d9%83%d8%a7%d8%af%d9%8a%d8%a7/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati3-scaled-500x760.jpg"
                ],
                "price" => 1400,
                "name" => "روزينها زورقي الصغير",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d9%88%d8%b2%d9%8a%d9%86%d9%87%d8%a7-%d8%b2%d9%88%d8%b1%d9%82%d9%8a-%d8%a7%d9%84%d8%b5%d8%ba%d9%8a%d8%b1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/05/note-t6-3-scaled-500x758.jpg"
                ],
                "price" => 1300,
                "name" => "دفاتر الورّاق",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%af%d9%81%d8%a7%d8%aa%d8%b1-%d8%a7%d9%84%d9%88%d8%b1%d9%91%d8%a7%d9%82/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/04/ss-scaled-500x750.jpg"
                ],
                "price" => 1400,
                "name" => "نازلة دار الأكابر",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%86%d8%a7%d8%b2%d9%84%d8%a9-%d8%af%d8%a7%d8%b1-%d8%a7%d9%84%d8%a3%d9%83%d8%a7%d8%a8%d8%b1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/04/hachacin-scaled-500x750.jpg"
                ],
                "price" => 1400,
                "name" => "قيامة الحشاشين",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%82%d9%8a%d8%a7%d9%85%d8%a9-%d8%a7%d9%84%d8%ad%d8%b4%d8%a7%d8%b4%d9%8a%d9%86/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/02/Rambo_Alhabshi-500x741.jpg"
                ],
                "price" => 700,
                "name" => "رامبو الحبشي",
                "category" => "الرواية العربية",
                "description" => "يسعى حجي جابر في هذه الرواية، إلى إعادة الاعتبار لإمرأة هررية رافقتْ آرتور رامبو في سنواته الأخيرة في الحبشة، دون أن يأتي الشاعر الفرنسي على ذكر عشيقته بكلمة واحدة في رسائله الكثيرة إلى أمّه، ولتسقط بذلك من كتب التأريخ. يمنح حجي الهررية اسماً وصوتاً وتأريخاً وذاكرة، ويمنحنا بالتالي فرصة لرؤية رامبو من وجهة نظر الأحباش، وكأنه بذلك يقلب الصورة فيُزيح رامبو إلى الهامش ويجلب العشيقة الحبشية إلى متن الحكاية، عبر سرد بعض ما جرى والكثير مما لم يحدث. إضافة إلى قصص الحب المبتورة، ومسارات الحكاية المتداخلة زمنياً، يتناول النصّ هرر، مدينة البنّ والقات حين كانت بمثابة مكة الإفريقية، يحرم على غير المسلمين دخولها، وتُنسج حولها الحكايات التي أغرتْ الرحالة من كل مكان، في وقت كانت القوى الكبرى تعيد تشكيل منطقة القرن الإفريقي.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%a7%d9%85%d8%a8%d9%88-%d8%a7%d9%84%d8%ad%d8%a8%d8%b4%d9%8a/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/02/ما-رواه-الرئيس-نهائي-cover-copy-500x773.png"
                ],
                "price" => 800,
                "name" => "ما رواه الرئيس",
                "category" => "الرواية العربية",
                "description" => "‮«‬كان‭ ‬الرئيس‭ ‬يتابع‭ ‬الحراك‭ ‬بعيون‭  ‬مبثوثة‭ ‬في‭ ‬كل‭ ‬مكان،‭ ‬وبين‭ ‬فينة‭ ‬وأخرى‭ ‬يُحذّر،‭ ‬بعين‭ ‬من‭ ‬خاض‭ ‬حرب‭ ‬التحرير‭ ‬وخبر‭  ‬عفن‭ ‬الساسة‭ ‬والنافذين‭ ‬من‭ ‬باعة‭ ‬الأوطان،‭ ‬من‭ ‬الانحراف‭ ‬به‭ ‬أو‭ ‬السطو‭ ‬عليه‭ ‬من‭  ‬الملتحقين‭ ‬في‭ ‬آخر‭ ‬لحظة‭ ‬أو‭ ‬أصحاب‭ ‬التوبة‭ ‬الزائفة،‭ ‬فتشكّ‭ ‬لحظةً‭ ‬في‭ ‬أنّه‭ ‬منتم‭  ‬إلى‭ ‬الحراك،‭ ‬لكنّه‭ ‬يفاجئك‭ ‬بأنّه‭ ‬يخوض‭ ‬حراكه‭ ‬الذاتي‭ ‬ضدّ‭ ‬موته‭ ‬ويحاول‭ ‬أن‭  ‬يُكفّر‭ ‬عن‭ ‬جُبنه‭ ‬وسكوته‭ ‬عن‭ ‬مصادرة‭ ‬استقلال‭ ‬الشعب‭ ‬في‭ ‬زمن‭ ‬الخضوع‭ ‬للزعيم‭  ‬المنتهي‭..‬‮»‬‭. ‬في‭ ‬هذه‭ ‬الرواية‭ ‬يلتقط‭ ‬الحبيب‭ ‬السائح‭ ‬لحظات‭ ‬من‭ ‬حراك‭ ‬22‭ ‬فيفري‭ ‬بالجزائر،‭ ‬تتعالى‭ ‬فيها‭ ‬هتافات‭ ‬الجماهير‭ ‬الغاضبة‭ ‬بشعارات‭ ‬ثوريّة‭ ‬وينساب‭ ‬صوت‭ ‬الرئيس‭ ‬من‭ ‬داخل‭ ‬غربته‭ ‬النفسية‭ ‬بما‭ ‬لم‭ ‬ينطق‭ ‬به‭ ‬من‭ ‬قبل‭. ‬ومن‭ ‬حوله‭ ‬الأستاذ‭ ‬المستكتب‭ ‬يتلقّف‭ ‬من‭ ‬فصول‭ ‬سيرته‭ ‬وجوها‭ ‬لا‭ ‬عهد‭ ‬له‭ ‬ولا‭ ‬للشعب‭ ‬بها‭.‬لكن‭ ‬لماذا‭ ‬اهتار‭ ‬الرئيس‭ ‬الأستاذ‭ ‬معين‭ ‬ليملي‭ ‬عليه‭ ‬سيرته؟‭ ‬ألأنه‭ ‬خبير‭ ‬في‭ ‬علوم‭ ‬الإعلام‭ ‬الإتصال‭  ‬وقد‭ ‬يفيده‭ ‬من‭ ‬هذه‭ ‬الجهة،‭ ‬أم‭ ‬لأنه‭ ‬وجد‭ ‬في‭ ‬نفسه‭ ‬موقعًا‭ ‬حسنًا،‭ ‬أم‭ ‬إنّ‭ ‬وراء‭ ‬ذلك‭ ‬سرّا‭ ‬دفينًا‭ ‬كتمه‭ ‬حتى‭ ‬غصّ‭ ‬به‭ ‬فاحتال‭ ‬لأمره‭ ‬كي‭ ‬لا‭ ‬يذهب‭ ‬إلى‭ ‬موته‭ ‬بأثقال‭ ‬لا‭ ‬يتّسع‭ ‬لها‭ ‬قبره‭ ‬المؤجّل‭.‬",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%85%d8%a7-%d8%b1%d9%88%d8%a7%d9%87-%d8%a7%d9%84%d8%b1%d8%a6%d9%8a%d8%b3/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/12/تت1ارر-500x750.jpg"
                ],
                "price" => 600,
                "name" => "قلب من طين",
                "category" => "الرواية العربية",
                "description" => "كانت الممرضة تقف عند النافذة، وتنتظر تقديم أي خدمة لمن تطلبها، ارتابت من تصرّفي فاقتربت مني بهدوء وسألتني: هل من مشكلة؟ أبحث عن أجنحتي.. (أجبت بوهن). شرّعت عينيها في وجهي ثم لاذت بالصمت، انصرفت بهدوء كما أتت، سمعتها بعدئذ تهمس لزميلتها « إنه تأثير الدواء!». أريد أجنحة تحلق بي لتنقذني من النزول إلى العالم السلفي. أولم تخبرنا الأسطورة أن «لكل أبناء الرب أجنحة»؟***اعتقني أيها المرض لقد أرهقتني أوجاع السياط التي تجلدني بها كل يوم، أريد حريتي، أريد جناحين أطير بهما مع صغاري وزوجي حيث بيتي الهادئ قبل أن يصبح ما هو عليه من فوضى، وقبل أن يصيروا إليه جميعهم إلى ما هم عليه اليوم من تشتت.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%82%d9%84%d8%a8-%d9%85%d9%86-%d8%b7%d9%8a%d9%86/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/22-500x776.jpg"
                ],
                "price" => 600,
                "name" => "شجرة العشق",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b4%d8%ac%d8%b1%d8%a9-%d8%a7%d9%84%d8%b9%d8%b4%d9%82/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/11-500x776.jpg"
                ],
                "price" => 900,
                "name" => "كيف ترسم زهرة الأوركيد",
                "category" => "الرواية العربية",
                "description" => "كيف ترسم زهرة الأوركيد هي رواية عن فنان مغمور، فتاة حالمة، ورجل يحاول تدارك أخطاءه. تنطلق أحداثها من محطة القطار، مهك الصدفة الأولى، التي تدفع بأبطال الرواية نحو سلسلة من الأحداث التي تتشابك فيها رغباتهم و مخاوفهم، وتصطدم بحقيقة الواقع والمجتمع الذي يعيشون فيها.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%83%d9%8a%d9%81-%d8%aa%d8%b1%d8%b3%d9%85-%d8%b2%d9%87%d8%b1%d8%a9-%d8%a7%d9%84%d8%a3%d9%88%d8%b1%d9%83%d9%8a%d8%af/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/33-500x776.jpg"
                ],
                "price" => 500,
                "name" => "إدلب",
                "category" => "الرواية العربية",
                "description" => "أصبحتُ عصبيًا أكثر مما كنت عليه، كانت رؤيتك تضمد الجروح داخلي، نسيت تصحيح شعوري الذي اختفى يبدوا أنني فقدت شهيتي للحياة، سأحاول أن أغني لكِ من بعيد وأنام في غربتي لقد تركت كل شيء في إدلب وبقايا قلبي لديكِ، أتيت إلى هنا بجثة ضخمة كيف لي أن أدمرك وأنا دون عائلة وعاداتنا لا يوجد بندٌ فيها لحماية حبنا تمنيت أن نتزوج ونبني أحلامنا معًا، سأظلمك وأظلم أطفالي في جلبهم لهذه المهزلة، يا حنين سأواصل السير حتى يبلعني البحر أو الأرض أو الحزن أحمل نفسي كحبل مشنقةٍ… كحبلٍ في يد طفل انتحر والداه توًا، حبيبتي هذا الذنب ليس ذنبنا، أكتبي عنّا يا حنين سأكون معك وأنت تعانقين الصغار في المخيمات، كلنا نعلم أنَّ لك جناحين ولكنك تفضلين السير معنا.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a5%d8%af%d9%84%d8%a8/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/cafè-riche-scaled-500x750.jpg"
                ],
                "price" => 600,
                "name" => "كافي ريش",
                "category" => "الرواية العربية",
                "description" => "هل تستطيع امرأة يهودية من الأقدام السوداء مثلي أن تكتب عن يوم رحيلها من الجزائر؟ من سيصدق؟ همست غزلان مرة أخيرة. وعاد الصدى يتردّد: ومن يهتم بغزلان وبأيام كافي ريش؟! شيماء، هي البداية والنهاية، شيماء هي الخير الذي عرفته وهي الشّر المُخبّأ في تفاصيل الحب التي أخفتها عني وأخفاها جون عنّي… طاردت غزلان كل التفاصيل في جزء من الثانية، اختصرت به الزمن والأحداث.استطاعت أن تكتب كلمة عربية “شيماء”. سريعا بعدها، حلّقت مع قطرات الحبر الأولى غيوم رمادية في سماء “كافي ريش”، اقترن سريعا اسم شيماء بالحب وبـ “جون” وبالمنظمة الخاصة الفرنسية. للحظةٍ، تحوّل الحنين الذي استهوى غزلان إلى شبه يقظة ضمير! لم تقو على محو صورة شيماء ولا مصيرها الذي انتهى على يد “طاسو”!-لا أحد عرف الحقيقة!لم يكن النادل يُخاطب غزلان ولا رواد “كافي ريش”، بل يُخاطب نفسه، مُستحضرا أول شخص فتح أبواب المقهى منذ قرابة قرن من الزمن. جاء مالك “كافي ريش” من بعيد، من بلادٍ في أقصى شرق المتوسط، لا يُدفن فيها الناس إلا وهم فلاسفة!",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%83%d8%a7%d9%81%d9%8a-%d8%b1%d9%8a%d8%b4/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/omar-500x729.jpg"
                ],
                "price" => 450,
                "name" => "القداس الأسود",
                "category" => "الرواية العربية",
                "description" => "يذهبُ آدم – وهو شخصية روحانيّة وغريبة لروائيّ شابْ غامر بحياته البسيطة لأنْ يخوض غمارًا في سبيل كتابة روايته العالقة والتي أخذت كلّ وقته ونفسيته – فوجد نفسهُ في حياة محفوفة بالمخاطر أين وقع في مشاكل عدة مع مجموعات غريبة ملثمة ذات خلفيات شيطانيّة وعاش صراعا بين ثالوق التشرد، روايته العالقة، وهذه الجماعة التي تبحث عنه لسبب ما!",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a7%d9%84%d9%82%d8%af%d8%a7%d8%b3-%d8%a7%d9%84%d8%a3%d8%b3%d9%88%d8%af/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/تالاسين-500x796.jpg"
                ],
                "price" => 400,
                "name" => "تالاسين حدائق الموت",
                "category" => "الرواية العربية",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%aa%d8%a7%d9%84%d8%a7%d8%b3%d9%8a%d9%86-%d8%ad%d8%af%d8%a7%d8%a6%d9%82-%d8%a7%d9%84%d9%85%d9%88%d8%aa/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/20210923104448841961-247x379.jpg"
                ],
                "price" => 1400,
                "name" => "السندباد الأعمى ؛ أطلس البحر والحرب",
                "category" => "الرواية العربية",
                "description" => "تدور الرواية حول حادثةٍ عرضية واحدة، تشكّل تحولًا جذريًا ونهائيًا لمصائر شخصياتٍ كانت تتّسمُ بالاكتراثِ والحلمِ والتفاعل، فتؤول بعدها إلى مسوخٍ لا تشبه بداياتها أبدًا. هذه رواية عن الحب والصداقة والخيانة، عن الالتزام السياسي والحرب، عن سقوط الشعارات وعن التناقضات في عالمِ فقد نقاءه إلى الأبد، حيثُ تنتهي تلك العناوين العريضة الى حيوات عبثية في عاديّتها. إنها رواية عن هؤلاء الذين ظنوا بأنهم مختلفين، ومسكونين بالالتزام والعقائدية، فإذا بحادثةٍ واحدة تقلبُ المشهد رأسًا على عقِب.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a7%d9%84%d8%b3%d9%86%d8%af%d8%a8%d8%a7%d8%af-%d8%a7%d9%84%d8%a3%d8%b9%d9%85%d9%89/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati3-scaled-500x760.jpg"
                ],
                "price" => 1400,
                "name" => "روزينها زورقي الصغير",
                "category" => "الرواية العربية",
                "description" => "رواية روزينها زورقي الصغير بقلم جوزيه ماورو .. ” روزينها زورقي الصغير” قصة غابات الأمازون بأدق دقائقها. يرويها جوزيه ماورو، صاحب “شجرتي، شجرة البرتقال الرائعة” بحرارة من تاه في تلك الغابات لحمًا ودمًا وذاكرة. يشق البطل زي أوروكو النهر على متن زورقه الصغير، روزينها.وليست روزينها كأي زورق، إنها رفيقة درب ومعلمة تلقن زي أوروكو ما لامست من دروس منذ أن كانت بذرة، فشجرة، فخشبًا يصير زورقًا. وهي رواية أيضًا، تُطلع صديقها زي أوروكو على قصص ساحرة تتيح للقارئ أن يلمس روح الغابة بكل مكوناتها. الغابة والنهر، كون روائي فريد، سحري وموقع بالأمطار والفيضان والشمس.نضحك مع هذه الرواية ونبكي، نعيش ونحلم. نتوه في كون طفولي عجيب، حيث يجانب البؤس الغرائبي وتؤاخي النعومة القسوة ويغدو كل عنصر موضوعًا للتساؤل ومادة للقصّ",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d9%88%d8%b2%d9%8a%d9%86%d9%87%d8%a7-%d8%b2%d9%88%d8%b1%d9%82%d9%8a-%d8%a7%d9%84%d8%b5%d8%ba%d9%8a%d8%b1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati2-scaled-500x736.jpg"
                ],
                "price" => 1500,
                "name" => "هيّا نوقظ الشّمس",
                "category" => "السرد",
                "description" => "طفل السادسة المصاب بحنان طافح يسيل من الأشياء البسيطة من حوله، المطل على عالم الكبار بأحلامه التي تشرق من شجرة برتقاله الرائعة، المربك لقواعدهم، الباحث فيها عن يد حانية وإن كانت وهمًا يرتعش على صفحة نهر وحيد، ها هو يبعد الآن عن عائلته وقد صار في الحادية عشرة، مفردًا، مصابًا بالحنين، مرتب الهندام، نظيفًا وباردًا من الوحدة، مشدودًا مثل وتر بين المدرسة الإعدادية ودروس البيانو. أي ثقل يمكن أن يزنه عالم كهذا على كتفي طفل ينزلق إلى المراهقة محملًا بذكريات الشوارع المغبرة والأزقة والدفء الحارق الذي يحوم حيث يسكن الفقر؟ كيف يشعر هذا الفتى وقد صار يسكن بيت عائلة جديدة ثريةـ تحول فيها من شيطان أزرق إلى ملاك مطيع؟ هل يظل على ذلك النحو، وقد صار قلبه الجديد يكلمه من داخله ويضيء عزلته بشعلة الأحلام ذاتها، ويخوص معاركه الصغيرة، وصولًا إلى لسعة الحب الأولى؟",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%87%d9%8a%d9%91%d8%a7-%d9%86%d9%88%d9%82%d8%b8-%d8%a7%d9%84%d8%b4%d9%91%d9%85%d8%b3/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati-scaled-500x734.jpg"
                ],
                "price" => 800,
                "name" => "شجرتي شجرة البرتقال الرائعة",
                "category" => "السرد",
                "description" => "من هذا الطفل الذي يناديه الجميع بالشيطان الصغير ويصفونه بقط المزاريب؟ وأيّ طفلٍ هذا الذي يحمل في قلبه عصفوراً يغني؟…“شجرتي شجرة البرتقال الرائعة” للكاتب خوسيه ماورودي فاسكونسيلوس عمل يُدّرس في المدارس البرازيلية وينصح الأساتذة في المعاهد الفرنسية طلبتهم بقراءته… إنه عمل مؤثّر وإنساني على لسان شاعرٍ طفلٍ لم يتجاوز عمُره خمس سنوات… عمل لا يروي حكاية خرافية ولا أحلام الصغار في البرازيل فحسب، بل يروي مغامرات الكاتب في طفولته، مغامرات الطفل الذي تعلم القراءة في سن الرابعة دون معلم، الطفل الذي يحمل في قلبه عصفوراً وفي رأسه شيطاناً يهمس له بأفكارٍ توقعه في المتاعب مع الكبار…هذه رواية عذبة عذوبة نسغ ثمرة برتقال حلوة… رواية إنسانية تصف البراءة التي يمكن لقلبٍ طفلٍ أن يحملها وتعرّفنا إلى روح الشاعر الفطرية… حكاية طفل يحمل دماء سكّان البرازيل الأصليّين، طفل يسرق كل صباح من حديقة أحد الأثرياء زهرةً لأجل معلّمته… وهو يتساءل بمنتهى البراءة: ألم يمنح الله الزهور لكل الناس؟…",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b4%d8%ac%d8%b1%d8%aa%d9%8a-%d8%b4%d8%ac%d8%b1%d8%a9-%d8%a7%d9%84%d8%a8%d8%b1%d8%aa%d9%82%d8%a7%d9%84-%d8%a7%d9%84%d8%b1%d8%a7%d8%a6%d8%b9%d8%a9/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/12/ىا-500x746.jpg"
                ],
                "price" => 450,
                "name" => "أقنعة الروح",
                "category" => "روايات مترجمة",
                "description" => "– هي رواية مشوقة وعاكسة للاجواء التعبيرية المميزة للاديب بار لاجيركفيست بروحيتها وغوصها في تقصي الحب والحقيقة وسر الحياة والموت ومصير الانسان، نقلها المترجم محمد بوطغان الى العربية والبسها حلة جميلة تزدان بها الثقافة الجزائرية، يخيل لك وانت تقرا الرواية كانك تقرا نصا مكتوبا في اصله بالعربية.(عبد الباقي هزرشي)– رواية تؤسس نفسها على الإنسان وعلى القيم المعنوية التي يفتقدها العالم الأروبي وغير الأروبي يوما بعد يوم. الموت ليس نهاية السعادة دائما، والحياة ليست مصدرها دائما.. الأجمل هو لغة الكاتب محمد بوطغان في الرواية الذي كانت له القدرة على ترويض الأداة اللغوية لتأتي الجملة سليمة مستقيمة، وحتى تصريف المثنى الذي تفرضه طبيعة الرواية وببدو ثقيلاً في اللغة العربية ويا ليْته ما كان، يألفه القارئ وينسجم معه من بداية النص إلى نهايته، فلا نحس بأية عقبة تعيق الاسترسال في القراءة.(خالدة. م)",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a3%d9%82%d9%86%d8%b9%d8%a9-%d8%a7%d9%84%d8%b1%d9%88%d8%ad/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/12/rajol-tibn-500x748.jpg"
                ],
                "price" => 500,
                "name" => "رجل التّبن يحرس الحقول الصفراء",
                "category" => "الشعر",
                "description" => "كُلُّ شَيْءٍ مُهَيَّأٌ للرَّحِيل..\nحتَّى الحَجَر اليّابِس فِي جَوْفِ الوَادِي،\nحتّى الوَادِي،\nيَرْحَلُ هُوَ الآخَرُ\nإلى بَحْرِ المِرْآةِ الأُخْرَى..\nحَتَّى المِرْآةُ الأخرَى\nتَرْحَلُ هي الأخرَى\nإليه.\n٠\n٠\n٠\nسَنَلْتَقِي\nجَمِيعًا\nهُنَاك..",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%ac%d9%84-%d8%a7%d9%84%d8%aa%d9%91%d8%a8%d9%86-%d9%8a%d8%ad%d8%b1%d8%b3-%d8%a7%d9%84%d8%ad%d9%82%d9%88%d9%84-%d8%a7%d9%84%d8%b5%d9%81%d8%b1%d8%a7%d8%a1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-أبي-الجبل-scaled-500x783.jpg"
                ],
                "price" => 600,
                "name" => "أبي الجبل",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a3%d8%a8%d9%8a-%d8%a7%d9%84%d8%ac%d8%a8%d9%84/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-رواية-رعاة-أركاديا-scaled-500x814.jpg"
                ],
                "price" => 700,
                "name" => "رعاة أركاديا",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%b9%d8%a7%d8%a9-%d8%a3%d8%b1%d9%83%d8%a7%d8%af%d9%8a%d8%a7/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati2-scaled-500x736.jpg"
                ],
                "price" => 1500,
                "name" => "هيّا نوقظ الشّمس",
                "category" => "السرد",
                "description" => "طفل السادسة المصاب بحنان طافح يسيل من الأشياء البسيطة من حوله، المطل على عالم الكبار بأحلامه التي تشرق من شجرة برتقاله الرائعة، المربك لقواعدهم، الباحث فيها عن يد حانية وإن كانت وهمًا يرتعش على صفحة نهر وحيد، ها هو يبعد الآن عن عائلته وقد صار في الحادية عشرة، مفردًا، مصابًا بالحنين، مرتب الهندام، نظيفًا وباردًا من الوحدة، مشدودًا مثل وتر بين المدرسة الإعدادية ودروس البيانو. أي ثقل يمكن أن يزنه عالم كهذا على كتفي طفل ينزلق إلى المراهقة محملًا بذكريات الشوارع المغبرة والأزقة والدفء الحارق الذي يحوم حيث يسكن الفقر؟ كيف يشعر هذا الفتى وقد صار يسكن بيت عائلة جديدة ثريةـ تحول فيها من شيطان أزرق إلى ملاك مطيع؟ هل يظل على ذلك النحو، وقد صار قلبه الجديد يكلمه من داخله ويضيء عزلته بشعلة الأحلام ذاتها، ويخوص معاركه الصغيرة، وصولًا إلى لسعة الحب الأولى؟",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%87%d9%8a%d9%91%d8%a7-%d9%86%d9%88%d9%82%d8%b8-%d8%a7%d9%84%d8%b4%d9%91%d9%85%d8%b3/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/chajarati-scaled-500x734.jpg"
                ],
                "price" => 800,
                "name" => "شجرتي شجرة البرتقال الرائعة",
                "category" => "السرد",
                "description" => "من هذا الطفل الذي يناديه الجميع بالشيطان الصغير ويصفونه بقط المزاريب؟ وأيّ طفلٍ هذا الذي يحمل في قلبه عصفوراً يغني؟…“شجرتي شجرة البرتقال الرائعة” للكاتب خوسيه ماورودي فاسكونسيلوس عمل يُدّرس في المدارس البرازيلية وينصح الأساتذة في المعاهد الفرنسية طلبتهم بقراءته… إنه عمل مؤثّر وإنساني على لسان شاعرٍ طفلٍ لم يتجاوز عمُره خمس سنوات… عمل لا يروي حكاية خرافية ولا أحلام الصغار في البرازيل فحسب، بل يروي مغامرات الكاتب في طفولته، مغامرات الطفل الذي تعلم القراءة في سن الرابعة دون معلم، الطفل الذي يحمل في قلبه عصفوراً وفي رأسه شيطاناً يهمس له بأفكارٍ توقعه في المتاعب مع الكبار…هذه رواية عذبة عذوبة نسغ ثمرة برتقال حلوة… رواية إنسانية تصف البراءة التي يمكن لقلبٍ طفلٍ أن يحملها وتعرّفنا إلى روح الشاعر الفطرية… حكاية طفل يحمل دماء سكّان البرازيل الأصليّين، طفل يسرق كل صباح من حديقة أحد الأثرياء زهرةً لأجل معلّمته… وهو يتساءل بمنتهى البراءة: ألم يمنح الله الزهور لكل الناس؟…",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b4%d8%ac%d8%b1%d8%aa%d9%8a-%d8%b4%d8%ac%d8%b1%d8%a9-%d8%a7%d9%84%d8%a8%d8%b1%d8%aa%d9%82%d8%a7%d9%84-%d8%a7%d9%84%d8%b1%d8%a7%d8%a6%d8%b9%d8%a9/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/12/bad-man-500x750.jpg"
                ],
                "price" => 400,
                "name" => "رجل سيئ السمعة",
                "category" => "السرد",
                "description" => "إنّ تناول الكاتب للتّفاصيل اليوميّة بإسقاط ترميزيّ وإيحاء بالعاديّة، ما هو إلاّ دعوة إلى استنهاضٍ للوعي المجتمعي، وتحفيز إلى اقتحام الواقع وتوديع الماضي بما له أو عليه… بما يشوبه أو يميّزه…\nأراد الكاتب أن يرفع الغطاء عن مجتمع يعاني تردّيات كبيرة، وسلّط الضّوء-الهادئ- على كثير من المواقع والنّماذج، بعين طفل يلهو مرَّة .. بعين شاب يعاني البطالة أو كهل يعاني التبطّل، لقد رصد الكثير من الصّور بدقّة وسلاسة الـ”المعتاد” وحيويّة الـ”يومي”.\nلا أحد ينتظرني كأنّي ميّت؛ وروحي معلّقة بين السّماء والأرض في زمن يأخذ من كل شيء وسط كل هذا الخراب، أبدو كنصف شخصٍ بربع قامة، أو كشاحنة أعصاب تحمل قُمامة الآخرين، أو كمسخٍ فرَّ من المقهى والشَّارع، وأنزوي في غرفتي التي تحتاج إلى طلاءٍ جديد، أحدّق إلى زاوية فأجد أن صديقا جديدا يحاول تقاسُم المأوى معي؛ عنكبوتٌ هزيلٌ يهرولُ مسرعا نحو بيته الوهِن يذكّرني بنفسي حينما كنتُ أركض لأجل لا شيء في اللّيالي الماطرة.مشعل عبّادي",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%ac%d9%84-%d8%b3%d9%8a%d8%a6-%d8%a7%d9%84%d8%b3%d9%85%d8%b9%d8%a9/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/غلاف-مجاز-السّرو-scaled-500x790.jpg"
                ],
                "price" => 500,
                "name" => "مجاز السّرو",
                "category" => "السرد",
                "description" => "هل كان يلزمه بوصلة حتى يستطيع لملمة ضياعه؟!\nلطالما كان “فنسنت” مؤمنا أن للألوان سطوة غريبة على روحه، يتغير مزاجه بِتَغيُّرِها، وتتّسع اللوحة أمامه فضاء متفهما. كنت أراه أكثر ابتهاجا على ضفاف حقل ما، بينما تكون الشمس قد أنارت العالم حوله، أقترب منه، لا يراني في غمرة انهماكه، يضرب بريشته اللوحة ضربات خفيفة، يطالعني وجهها مبتهجا بأشجار السّرو التي تحوطه، ولكن تبقى تلك من المرات القليلة التي يعتدل فيها مزاجه.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/auto-draft/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/55-500x776.jpg"
                ],
                "price" => 450,
                "name" => "يوم التقيت ألبيرتومورافيا في الصحراء",
                "category" => "السرد",
                "description" => "تسحرنا الرّحلات وتفاصيلها. وتصبح أكثرَ سحرًا إذا التقطت هذه التّفاصيلَ عين فضوليّة بقلم ذي لغة سلسة. وهذا ما يوفّره لنا هذا الكتاب الذي تّوِّج بجائزة محمّد ولد الشّيخ لنصوص الصّحراء.لقد تحالف حسّ الصّحفيّة مع حسّ القاصّة في زهية منصر، فطلعت رحلتها إلى الصّحراء الجزائرية شبيهةً بمركبة سحريّة تسافر بنا من غير أن نغادر أماكننا. وتلك وظيفة الكتابة: التّحريض على السّفر.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b9%d9%86%d8%af%d9%85%d8%a7-%d8%a7%d9%84%d8%aa%d9%82%d9%8a%d8%aa-%d8%a3%d9%84%d8%a8%d9%8a%d8%b1%d8%aa%d9%88%d9%85%d9%88%d8%b1%d8%a7%d9%81%d9%8a%d8%a7-%d9%81%d9%8a-%d8%a7%d9%84%d8%b5%d8%ad%d8%b1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/ظل-الموت-1-scaled-500x769.jpg"
                ],
                "price" => 400,
                "name" => "ظل الموت",
                "category" => "السرد",
                "description" => "رسالتكَ الأخيرة التي دسستها بين أكمام الغياب، قرأتُها ألف مرة، حتّى أعيتني حروفها وأرهقتني سطورهاو بتّ أرتّلها بوعي منّي أو بدون..رسالةٌ منك أوقدت بذاكرتي كلّ معزوفةِ حبٍّ بيننا، كلّ رقصةهيامٍ قمنا بها، كلّ كلمةٍ منك تتيمتُ وتيمّنتَُ بها: 《إلى الحبّ الذي لن أنساه، و سأحمله بقلبي أبد العيش..أمّا بعد، فأنا سأركل حياة المشرق هاهنا؛ و سأرحلُ إلى ضفاف البحر، أين سأصطاد حلمي، بعيدًا عن مجتمعٍ أتخمني بقيمٍ زائفةو ديانةٍ تمنعني من أن أمارس ما أحب وفق ما أحب، سوف أمتنعُ أخيرا عن تقاليد المجتمع العمياء، التي طالما منعتني من امتهان موهبتي..وحدها السّماء ستشهدُ بأنّني بلغتُ حلمي، في بلادٍ ستمنحني حقّ الحلم و حقّ الحياة..أدركُ مرّ حروفي في حلقكِ و أنتِ تقرئين رسالتي هذه، لذا لن أطلب منكِ الآن بأن تغفري لي بُعادي؛ لأنّني على يقين بأنّكِِ حتما ستفعلين ذلك لاحقا… محبّتي",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b8%d9%84-%d8%a7%d9%84%d9%85%d9%88%d8%aa/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-سيكولوجية-الجماهير-scaled-500x826.jpg"
                ],
                "price" => 600,
                "name" => "سيكولوجية الجماهير",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b3%d9%8a%d9%83%d9%88%d9%84%d9%88%d8%ac%d9%8a%d8%a9-%d8%a7%d9%84%d8%ac%d9%85%d8%a7%d9%87%d9%8a%d8%b1/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-أبي-الجبل-scaled-500x783.jpg"
                ],
                "price" => 600,
                "name" => "أبي الجبل",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%a3%d8%a8%d9%8a-%d8%a7%d9%84%d8%ac%d8%a8%d9%84/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/09/غلاف-رواية-رعاة-أركاديا-scaled-500x814.jpg"
                ],
                "price" => 700,
                "name" => "رعاة أركاديا",
                "category" => "الادب",
                "description" => "“في مرحلة مهمّة من تاريخ الجزائر امتدّت بين 1958 و1962 ومع خروج آخر جنديّ فرنسي من الجزائر المستقلّة تعود بنا أركاديا بيشار سليلة الأقدام السوداء إلى تلك الحقبة مستحضرة بألم وحنين ذكرياتها في فترة شبابها حينما تعلّقت جسدا وروحا بأوجي فرو أحد أبناء الجزائر الكولونيالية، تسترجع بعد نصف قرن ماضيها المعقّد عبر مذكّرات حبلى بالمفارقات-كالألم والحب- والتي بقدر ما ضمّت اوجاعا ومآسي وخراب بقدر ما تركت أثارا-نوستالجية- رسمتها تفاصيل حب مستحيل بين شاب جزائري وامرأة فرنسية.هل الجزائر فرنسية؟ هل ثمّة من أحبّ الجزائر وضحّى من أجلها أكثر من الجزائريين أنفسهم من سطّر تاريخ الجزائر إبّان الاحتلال الفرنسي ؟ وما الوشائج الرّاسخة في ذاكرة سيّدات من أصول فرنسية؟ هذه الأسئلة وغيرها تجيب عنها رواية رعاة أركاديا، رواية النبش في الذاكرة”",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b1%d8%b9%d8%a7%d8%a9-%d8%a3%d8%b1%d9%83%d8%a7%d8%af%d9%8a%d8%a7/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2021/03/ص-ب-500x767.jpg"
                ],
                "price" => 450,
                "name" => "ص.ب السّماء السابعة",
                "category" => "الادب",
                "description" => "عَزيزي الله،\nأنَا كائنٌ ضَئيلٌ وَسط هذا الكَون الواسِع، ذَرّةٌ وَسط الكَثيرِ من الذرّات المُتماثلَة في الحَجم، شَيءٌ لَا يَختلفُ ظاهرِيًّا عَن بقيّة الأشياء.\nأحدّثكَ من الأسفَل؛ حَيث كُلُّ الأشياءِ وَالوجوه مُتشابهَة، وَحَيث لَا أحدَ يُلاحظ أحدًا، حَيث الظّاهِرُ أهمّ مِن الباطِن وَالصُّوَرُ أهمّ من النّيّات، حَيث الجَميع يَنظرون إلى نَقائِصِ بعضهم البَعض وَحَيث الأحكَامُ تُبنَى عَلى عدم مَعرفةٍ أو قلّتِها.\nأنجِدْني، يا الله ! اِنتَشِلْني من ضَياعِي ! ساعِدنِي لِأعرِفَنِي، أقرَأنِي، أفهَمني… دُون شَرحٍ أو تَشرِيح.\nأنَا تائِهةٌ بِامتِدادِ ما خَلَقتَه وَمَا أنتَ قادِرٌ على خَلقِه، يا رَبّ!\nوَأرجُو منكَ إشارةً، دَليلًا، سَبيلًا… يَقُودُنِي إلَيك.",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b5-%d8%a8-%d8%a7%d9%84%d8%b3%d9%91%d9%85%d8%a7%d8%a1-%d8%a7%d9%84%d8%b3%d8%a7%d8%a8%d8%b9%d8%a9/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/12/gardinia-copy-500x750.jpg"
                ],
                "price" => 350,
                "name" => "غاردينيا",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%ba%d8%a7%d8%b1%d8%af%d9%8a%d9%86%d9%8a%d8%a7/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/09/66-500x776.jpg"
                ],
                "price" => 400,
                "name" => "نصّي الثّالث",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%86%d8%b5%d9%91%d9%8a-%d8%a7%d9%84%d8%ab%d9%91%d8%a7%d9%84%d8%ab/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/yaser-karek-500x769.jpg"
                ],
                "price" => 450,
                "name" => "تحت أعين الملائكة",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%aa%d8%ad%d8%aa-%d8%a3%d8%b9%d9%8a%d9%86-%d8%a7%d9%84%d9%85%d9%84%d8%a7%d8%a6%d9%83%d8%a9/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/غلاف-شيء-ما-500x767.jpg"
                ],
                "price" => 350,
                "name" => "شيء ما",
                "category" => "الادب",
                "description" => "",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d8%b4%d9%8a%d8%a1-%d9%85%d8%a7/"
            ],
            [
                "pictures" => [
                    "https://dammahpub.com/wp-content/uploads/2020/07/نجمة-طرية-500x806.jpg"
                ],
                "price" => 450,
                "name" => "نجمة طرية",
                "category" => "الادب",
                "description" => "القصيدة ابنتيبولادة عسيرةثمأصبح لي ديوان، قصيدة على الأريكة،على الثلاجة،على رأس السرير،تحت الخزانة، أخرى مع جلد الغبار في سلة الغسيل، في الغسالة، أخرى معلقة على السطح كجثةعلى الباب الخارجيأخرى أصافحه بها قصيدة على البحرقصيدة في يديتوأمها في قلبهقصيدة كلحظة عناق تُقرأ في لحظات ضعفه أطلبها بعمر بكائي.على الدفتر العائليكتب “أبو القصائد، أم القافيات”",
                "maisonEdition" => "ضمة للنشر والتوزيع",
                "stockDispo" => 100,
                "isBook" => true,
                "magasinId" => 381,
                "isPublished" => true,
                "href" => "https://dammahpub.com/product/%d9%86%d8%ac%d9%85%d8%a9-%d8%b7%d8%b1%d9%8a%d8%a9/"
            ]

        ];
        for ($i = 0 ; $i < sizeof($books); $i++) {
            $book = $books[$i];
            if (isset($book['name']) && isset($book['category'])) {
                $existedProduct = Product::where('name' , $book['name'])
                    ->where('maisonEdition' , $book['maisonEdition'])->count();
                if ($existedProduct === 0) {
                    Product::create([
                        "pictures" => json_encode($book['pictures']),
                        "price" => $book['price'],
                        "name" => $book['name'],
                        "category" => $book['category'],
                        "description" => $book['description'],
                        "maisonEdition" => $book['maisonEdition'],
                        "stockDispo" => $book['stockDispo'],
                        "isBook" => true,
                        "magasinId" => 381,
                        "isPublished" => true,
                        "isFreeDelivery" => false
                    ]);

                }
            }
        }

        return 'dammah books created!';

    }

    public function getAuthors() {
        $authors = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereNotNull('pictures')
            ->whereHas('store')
            ->whereNotNull('author')
            ->groupBy('author')
            ->select('author')
            ->limit(20)
            ->get();

        return $authors;
    }

    public function getBookCategories() {
        $categories = Product::where('isBook' , 1)
            ->where('isPublished' , 1)
            ->whereNotNull('pictures')
            ->whereHas('store')
            ->whereNotNull('category')
            ->groupBy('category')
            ->select('category')
            ->limit(20)
            ->get();

        return $categories;
    }

    public function disableBooks(Request $request, $id) {

        $editor = Edition::where('id' , $id)->first();

        Product::where('maisonEdition' , $editor->maisonEdition)->update(['isPublished' => 0]);

        return 'books for ' . $editor->maisonEdition . ' disabled successfully!';

    }

    public function enableBooks(Request $request, $id) {

        $editor = Edition::where('id' , $id)->first();

        Product::where('maisonEdition' , $editor->maisonEdition)->update(['isPublished' => 1]);

        return 'books for ' . $editor->maisonEdition . ' disabled successfully!';

    }
}
