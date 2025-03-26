<?php

namespace App\Models;

use App\Enums\Status;
// use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
// use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model 
{
    // use HasFactory, InteractsWithMedia, SoftDeletes;
    use HasFactory ,SoftDeletes;

    protected $table = "products";

    protected $fillable = [
        'name', 'slug', 'sku', 'product_category_id', 'product_brand_id',
        'barcode_id', 'unit_id', 'buying_price', 'selling_price', 
        'variation_price', 'status', 'order', 'can_purchasable', 
        'show_stock_out', 'maximum_purchase_quantity', 
        'low_stock_quantity_warning', 'weight', 'refundable', 
        'description', 'shipping_and_return', 'add_to_flash_sale', 
        'discount', 'offer_start_date', 'offer_end_date', 
        'shipping_type', 'shipping_cost', 'is_product_quantity_multiply',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id'                           => 'integer',
        'name'                         => 'string',
        'slug'                         => 'string',
        'sku'                          => 'string',
        'product_category_id'          => 'integer',
        'product_brand_id'             => 'integer',
        'barcode_id'                   => 'string',
        'unit_id'                      => 'integer',
        'buying_price'                 => 'decimal:6',
        'selling_price'                => 'decimal:6',
        'variation_price'              => 'decimal:6',
        'status'                       => 'integer',
        'order'                        => 'integer',
        'can_purchasable'              => 'integer',
        'show_stock_out'               => 'integer',
        'maximum_purchase_quantity'    => 'integer',
        'low_stock_quantity_warning'   => 'integer',
        'weight'                       => 'string',
        'refundable'                   => 'integer',
        'description'                  => 'string',
        'shipping_and_return'          => 'string',
        'add_to_flash_sale'            => 'integer',
        'discount'                     => 'decimal:6',
        'offer_start_date'             => 'string',
        'offer_end_date'               => 'string',
        'shipping_type'                => 'integer',
        'shipping_cost'                => 'string',
        'is_product_quantity_multiply' => 'integer',
    ];

    /**
     * Scopes
     */
    public function scopeActive($query, $col = 'status')
    {
        return $query->where($col, Status::ACTIVE);
    }

    public function scopeRandAndLimitOrOrderBy($query, $rand = 0, $orderColumn = 'id', $orderType = 'asc')
    {
        if ($rand > 0) {
            return $query->inRandomOrder()->limit($rand);
        }
        return $query->orderBy($orderColumn, $orderType);
    }

    /**
     * Accessors for Media
     */
    public function getImageAttribute(): string
    {
        if ($url = $this->getFirstMediaUrl('product')) {
            return asset($url);
        }
        return asset('images/default/product/thumb.png');
    }

    public function getThumbAttribute(): string
    {
        if ($media = $this->getMedia('product')->first()) {
            return $media->getUrl('thumb');
        }
        return asset('images/default/product/thumb.png');
    }

    public function getCoverAttribute(): string
    {
        if ($media = $this->getMedia('product')->first()) {
            return $media->getUrl('cover');
        }
        return asset('images/default/product/cover.png');
    }

    public function getPreviewAttribute(): string
    {
        if ($media = $this->getMedia('product')->first()) {
            return $media->getUrl('preview');
        }
        return asset('images/default/product/preview.png');
    }

    public function getImagesAttribute(): array
    {
        $response = [];
        foreach ($this->getMedia('product') as $image) {
            $response[] = $image->getUrl();
        }
        return $response;
    }

    /**
     * Media Conversions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->crop('crop-center', 112, 120)
            ->keepOriginalImageFormat()
            ->sharpen(10);

        $this->addMediaConversion('cover')
            ->crop('crop-center', 248, 270)
            ->keepOriginalImageFormat()
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->crop('crop-center', 1024, 1024)
            ->keepOriginalImageFormat()
            ->sharpen(10);
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->with('productAttribute');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id');
    }

    // public function stocks()
    // {
    //     return $this->morphMany(Stock::class, 'model');
    // }

   
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'product_id');
    }


    public function tags()
    {
        return $this->hasMany(ProductTag::class, 'product_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class, 'product_id', 'id');
    }

    public function seo()
    {
        return $this->hasOne(ProductSeo::class, 'product_id', 'id');
    }

    public function wishlist()
    {
        return $this->hasOne(Wishlist::class);
    }

    public function userReview()
    {
        return $this->hasOne(ProductReview::class, 'product_id', 'id')
            ->where('user_id', Auth::id());
    }

    public function productTaxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function productOrders()
    {
        return $this->hasMany(Stock::class, 'product_id', 'id')
            ->where('model_type', Order::class);
    }
}
