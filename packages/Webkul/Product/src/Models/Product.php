<?php

namespace Webkul\Product\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Shetabit\Visitor\Traits\Visitable;
use Webkul\Attribute\Models\AttributeFamilyProxy;
use Webkul\Attribute\Models\AttributeProxy;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\BookingProduct\Models\BookingProductProxy;
use Webkul\CatalogRule\Models\CatalogRuleProductPriceProxy;
use Webkul\Category\Models\CategoryProxy;
use Webkul\Core\Models\ChannelProxy;
use Webkul\Inventory\Models\InventorySourceProxy;
use Webkul\Product\Contracts\Product as ProductContract;
use Webkul\Product\Database\Factories\ProductFactory;
use Webkul\Product\Type\AbstractType;

class Product extends Model implements ProductContract
{
    use HasFactory, Visitable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'attribute_family_id',
        'sku',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'additional' => 'array',
    ];

    /**
     * The type of product.
     *
     * @var \Webkul\Product\Type\AbstractType
     */
    protected $typeInstance;

    /**
     * Get the product flat entries that are associated with product.
     * May be one for each locale and each channel.
     */
    public function product_flats(): HasMany
    {
        return $this->hasMany(ProductFlatProxy::modelClass(), 'product_id');
    }

    /**
     * Get the product that owns the product.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * Get the product attribute family that owns the product.
     */
    public function attribute_family(): BelongsTo
    {
        return $this->belongsTo(AttributeFamilyProxy::modelClass());
    }

    /**
     * The super attributes that belong to the product.
     */
    public function super_attributes(): BelongsToMany
    {
        return $this->belongsToMany(AttributeProxy::modelClass(), 'product_super_attributes');
    }

    /**
     * Get the product attribute values that owns the product.
     */
    public function attribute_values(): HasMany
    {
        return $this->hasMany(ProductAttributeValueProxy::modelClass());
    }

    /**
     * Get the product customer group prices that owns the product.
     */
    public function customer_group_prices(): HasMany
    {
        return $this->hasMany(ProductCustomerGroupPriceProxy::modelClass());
    }

    /**
     * Get the product customer group prices that owns the product.
     */
    public function catalog_rule_prices(): HasMany
    {
        return $this->hasMany(CatalogRuleProductPriceProxy::modelClass());
    }

    /**
     * Get the price indices that owns the product.
     */
    public function price_indices(): HasMany
    {
        return $this->hasMany(ProductPriceIndexProxy::modelClass());
    }

    /**
     * Get the inventory indices that owns the product.
     */
    public function inventory_indices(): HasMany
    {
        return $this->hasMany(ProductInventoryIndexProxy::modelClass());
    }

    /**
     * The categories that belong to the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CategoryProxy::modelClass(), 'product_categories');
    }

    /**
     * The images that belong to the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImageProxy::modelClass(), 'product_id')
            ->orderBy('position');
    }

    /**
     * The videos that belong to the product.
     */
    public function videos(): HasMany
    {
        return $this->hasMany(ProductVideoProxy::modelClass(), 'product_id')
            ->orderBy('position');
    }

    /**
     * Get the product reviews that owns the product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReviewProxy::modelClass());
    }

    /**
     * Get the approved product reviews.
     */
    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('status', 'approved');
    }

    /**
     * The inventory sources that belong to the product.
     */
    public function inventory_sources(): BelongsToMany
    {
        return $this->belongsToMany(InventorySourceProxy::modelClass(), 'product_inventories')
            ->withPivot('id', 'qty');
    }

    /**
     * Get inventory source quantity.
     *
     * @return bool
     */
    public function inventory_source_qty($inventorySourceId)
    {
        return $this->inventories()
            ->where('inventory_source_id', $inventorySourceId)
            ->sum('qty');
    }

    /**
     * The inventories that belong to the product.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(ProductInventoryProxy::modelClass(), 'product_id');
    }

    /**
     * The ordered inventories that belong to the product.
     */
    public function ordered_inventories(): HasMany
    {
        return $this->hasMany(ProductOrderedInventoryProxy::modelClass(), 'product_id');
    }

    /**
     * Get the customizable options.
     */
    public function customizable_options(): HasMany
    {
        return $this->hasMany(ProductCustomizableOptionProxy::modelClass())
            ->orderBy('sort_order');
    }

    /**
     * Get the product variants that owns the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Get the grouped products that owns the product.
     */
    public function grouped_products(): HasMany
    {
        return $this->hasMany(ProductGroupedProductProxy::modelClass());
    }

    /**
     * Get the grouped products that owns the product.
     */
    public function booking_products(): HasMany
    {
        return $this->hasMany(BookingProductProxy::modelClass());
    }

    /**
     * The images that belong to the product.
     */
    public function downloadable_samples(): HasMany
    {
        return $this->hasMany(ProductDownloadableSampleProxy::modelClass());
    }

    /**
     * The images that belong to the product.
     */
    public function downloadable_links(): HasMany
    {
        return $this->hasMany(ProductDownloadableLinkProxy::modelClass());
    }

    /**
     * Get the bundle options that owns the product.
     */
    public function bundle_options(): HasMany
    {
        return $this->hasMany(ProductBundleOptionProxy::modelClass());
    }

    /**
     * The related products that belong to the product.
     */
    public function related_products(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'product_relations', 'parent_id', 'child_id');
    }

    /**
     * The up sells that belong to the product.
     */
    public function up_sells(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'product_up_sells', 'parent_id', 'child_id');
    }

    /**
     * The cross sells that belong to the product.
     */
    public function cross_sells(): BelongsToMany
    {
        return $this->belongsToMany(static::class, 'product_cross_sells', 'parent_id', 'child_id');
    }

    /**
     * The cross sells that belong to the product.
     */
    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(ChannelProxy::modelClass(), 'product_channels', 'product_id', 'channel_id');
    }

    /**
     * Is saleable.
     *
     * @param  string  $key
     *
     * @throws \Exception
     */
    public function isSaleable(): bool
    {
        return $this->getTypeInstance()
            ->isSaleable();
    }

    /**
     * Is stockable.
     *
     *
     * @throws \Exception
     */
    public function isStockable(): bool
    {
        return $this->getTypeInstance()
            ->isStockable();
    }

    /**
     * Total quantity.
     *
     *
     * @throws \Exception
     */
    public function totalQuantity(): int
    {
        return $this->getTypeInstance()
            ->totalQuantity();
    }

    /**
     * Have sufficient quantity.
     *
     *
     * @throws \Exception
     */
    public function haveSufficientQuantity(int $qty): bool
    {
        return $this->getTypeInstance()
            ->haveSufficientQuantity($qty);
    }

    /**
     * Get type instance.
     *
     *
     * @throws \Exception
     */
    public function getTypeInstance(): AbstractType
    {
        if ($this->typeInstance) {
            return $this->typeInstance;
        }

        $this->typeInstance = app(config('product_types.'.$this->type.'.class'));

        if (! $this->typeInstance instanceof AbstractType) {
            throw new Exception("Please ensure the product type '{$this->type}' is configured in your application.");
        }

        $this->typeInstance->setProduct($this);

        return $this->typeInstance;
    }

    /**
     * The images that belong to the product.
     *
     * @return string
     */
    public function getBaseImageUrlAttribute()
    {
        $image = $this->images->first();

        return $image->url ?? null;
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        // #region agent log
        if ($key === 'url_key' && isset($this->id)) {
            file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_B','timestamp'=>time()*1000,'location'=>'Product.php:385','message'=>'Product getAttribute - url_key accessed','data'=>['product_id'=>$this->id,'key'=>$key,'has_attribute_in_cache'=>isset($this->attributes[$key]),'attribute_family_id'=>$this->attribute_family_id],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B'])."\n", FILE_APPEND);
        }
        // #endregion

        if (! method_exists(static::class, $key)
            && ! in_array($key, [
                'pivot',
                'parent_id',
                'attribute_family_id',
            ])
            && ! isset($this->attributes[$key])
        ) {
            if (isset($this->id)) {
                $attribute = $this->checkInLoadedFamilyAttributes()->where('code', $key)->first();

                // #region agent log
                if ($key === 'url_key') {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_C','timestamp'=>time()*1000,'location'=>'Product.php:396','message'=>'Product getAttribute - attribute found in family','data'=>['product_id'=>$this->id,'attribute_found'=>!is_null($attribute),'attribute_id'=>$attribute?->id,'attribute_code'=>$attribute?->code],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'C'])."\n", FILE_APPEND);
                }
                // #endregion

                $this->attributes[$key] = $this->getCustomAttributeValue($attribute);

                // #region agent log
                if ($key === 'url_key') {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_D','timestamp'=>time()*1000,'location'=>'Product.php:398','message'=>'Product getAttribute - custom attribute value retrieved','data'=>['product_id'=>$this->id,'url_key_value'=>$this->attributes[$key],'url_key_is_null'=>is_null($this->attributes[$key])],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'D'])."\n", FILE_APPEND);
                }
                // #endregion

                return $this->getAttributeValue($key);
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * Retrieve product attributes.
     *
     * @param  Group  $group
     * @param  bool  $skipSuperAttribute
     *
     * @throws \Exception
     */
    public function getEditableAttributes($group = null, $skipSuperAttribute = true): Collection
    {
        return $this->getTypeInstance()
            ->getEditableAttributes($group, $skipSuperAttribute);
    }

    /**
     * Get an product attribute value.
     *
     * @return mixed
     */
    public function getCustomAttributeValue($attribute)
    {
        if (! $attribute) {
            // #region agent log
            if (isset($this->id)) {
                file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_E','timestamp'=>time()*1000,'location'=>'Product.php:428','message'=>'Product getCustomAttributeValue - attribute is null','data'=>['product_id'=>$this->id],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n", FILE_APPEND);
            }
            // #endregion
            return;
        }

        $locale = core()->getRequestedLocaleCodeInRequestedChannel();

        $channel = core()->getRequestedChannelCode();

        // #region agent log
        $isUrlKey = ($attribute->code ?? '') === 'url_key';
        if ($isUrlKey && isset($this->id)) {
            $urlKeyValues = $this->attribute_values->where('attribute_id', $attribute->id)->map(function($av) {
                return ['id' => $av->id, 'locale' => $av->locale, 'channel' => $av->channel, 'text_value' => $av->text_value];
            })->values()->toArray();
            file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_F','timestamp'=>time()*1000,'location'=>'Product.php:432','message'=>'Product getCustomAttributeValue - starting search','data'=>['product_id'=>$this->id,'attribute_id'=>$attribute->id,'attribute_code'=>$attribute->code,'locale'=>$locale,'channel'=>$channel,'value_per_channel'=>$attribute->value_per_channel??false,'value_per_locale'=>$attribute->value_per_locale??false,'attribute_values_count'=>$this->attribute_values->count(),'url_key_values_in_db'=>$urlKeyValues],'sessionId'=>'debug-session','runId'=>'run2','hypothesisId'=>'F'])."\n", FILE_APPEND);
        }
        // #endregion

        if (empty($this->attribute_values->count())) {
            $this->load('attribute_values');
        }

        if ($attribute->value_per_channel) {
            if ($attribute->value_per_locale) {
                $attributeValue = $this->attribute_values
                    ->where('channel', $channel)
                    ->where('locale', $locale)
                    ->where('attribute_id', $attribute->id)
                    ->first();

                // #region agent log
                if ($isUrlKey && isset($this->id)) {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_G','timestamp'=>time()*1000,'location'=>'Product.php:442','message'=>'Product getCustomAttributeValue - searched channel+locale','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'G'])."\n", FILE_APPEND);
                }
                // #endregion

                if (empty($attributeValue) || empty($attributeValue[$attribute->column_name])) {
                    $attributeValue = $this->attribute_values
                        ->where('channel', core()->getDefaultChannelCode())
                        ->where('locale', core()->getDefaultLocaleCodeFromDefaultChannel())
                        ->where('attribute_id', $attribute->id)
                        ->first();

                    // #region agent log
                    if ($isUrlKey && isset($this->id)) {
                        file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_H','timestamp'=>time()*1000,'location'=>'Product.php:449','message'=>'Product getCustomAttributeValue - fallback to default channel+locale','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'H'])."\n", FILE_APPEND);
                    }
                    // #endregion

                    // Fallback final: buscar em qualquer locale disponível (especialmente importante para url_key)
                    if (empty($attributeValue) || empty($attributeValue[$attribute->column_name])) {
                        $attributeValue = $this->attribute_values
                            ->where('attribute_id', $attribute->id)
                            ->whereNotNull($attribute->column_name)
                            ->first();

                        // #region agent log
                        if ($isUrlKey && isset($this->id)) {
                            file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_O','timestamp'=>time()*1000,'location'=>'Product.php:496','message'=>'Product getCustomAttributeValue - fallback to any available locale (channel+locale case)','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null,'locale_found'=>$attributeValue->locale??null],'sessionId'=>'debug-session','runId'=>'run2','hypothesisId'=>'O'])."\n", FILE_APPEND);
                        }
                        // #endregion
                    }
                }
            } else {
                $attributeValue = $this->attribute_values
                    ->where('channel', $channel)
                    ->where('attribute_id', $attribute->id)
                    ->first();

                // #region agent log
                if ($isUrlKey && isset($this->id)) {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_I','timestamp'=>time()*1000,'location'=>'Product.php:456','message'=>'Product getCustomAttributeValue - searched channel only','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'I'])."\n", FILE_APPEND);
                }
                // #endregion
            }
        } else {
            if ($attribute->value_per_locale) {
                $attributeValue = $this->attribute_values
                    ->where('locale', $locale)
                    ->where('attribute_id', $attribute->id)
                    ->first();

                // #region agent log
                if ($isUrlKey && isset($this->id)) {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_J','timestamp'=>time()*1000,'location'=>'Product.php:463','message'=>'Product getCustomAttributeValue - searched locale only','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'J'])."\n", FILE_APPEND);
                }
                // #endregion

                if (empty($attributeValue) || empty($attributeValue[$attribute->column_name])) {
                    $attributeValue = $this->attribute_values
                        ->where('locale', core()->getDefaultLocaleCodeFromDefaultChannel())
                        ->where('attribute_id', $attribute->id)
                        ->first();

                    // #region agent log
                    if ($isUrlKey && isset($this->id)) {
                        file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_K','timestamp'=>time()*1000,'location'=>'Product.php:469','message'=>'Product getCustomAttributeValue - fallback to default locale','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'K'])."\n", FILE_APPEND);
                    }
                    // #endregion

                    // Fallback final: buscar em qualquer locale disponível (especialmente importante para url_key)
                    if (empty($attributeValue) || empty($attributeValue[$attribute->column_name])) {
                        $attributeValue = $this->attribute_values
                            ->where('attribute_id', $attribute->id)
                            ->whereNotNull($attribute->column_name)
                            ->first();

                        // #region agent log
                        if ($isUrlKey && isset($this->id)) {
                            file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_N','timestamp'=>time()*1000,'location'=>'Product.php:477','message'=>'Product getCustomAttributeValue - fallback to any available locale','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null,'locale_found'=>$attributeValue->locale??null],'sessionId'=>'debug-session','runId'=>'run2','hypothesisId'=>'N'])."\n", FILE_APPEND);
                        }
                        // #endregion
                    }
                }
            } else {
                $attributeValue = $this->attribute_values
                    ->where('attribute_id', $attribute->id)
                    ->first();

                // #region agent log
                if ($isUrlKey && isset($this->id)) {
                    file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_L','timestamp'=>time()*1000,'location'=>'Product.php:475','message'=>'Product getCustomAttributeValue - searched by attribute_id only','data'=>['product_id'=>$this->id,'attribute_value_found'=>!is_null($attributeValue),'value'=>$attributeValue[$attribute->column_name]??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'L'])."\n", FILE_APPEND);
                }
                // #endregion
            }
        }

        $result = $attributeValue[$attribute->column_name] ?? $attribute->default_value;

        // #region agent log
        if ($isUrlKey && isset($this->id)) {
            file_put_contents('/Users/guilhermewestrup/projetos/labrumaloja.com.br/.cursor/debug.log', json_encode(['id'=>'log_'.time().'_M','timestamp'=>time()*1000,'location'=>'Product.php:481','message'=>'Product getCustomAttributeValue - final result','data'=>['product_id'=>$this->id,'result'=>$result,'result_is_null'=>is_null($result),'default_value'=>$attribute->default_value],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'M'])."\n", FILE_APPEND);
        }
        // #endregion

        return $result;
    }

    /**
     * Attributes to array.
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        $hiddenAttributes = $this->getHidden();

        if (isset($this->id)) {
            $familyAttributes = $this->checkInLoadedFamilyAttributes();

            foreach ($familyAttributes as $attribute) {
                if (in_array($attribute->code, $hiddenAttributes)) {
                    continue;
                }

                $attributes[$attribute->code] = $this->getCustomAttributeValue($attribute);
            }
        }

        return $attributes;
    }

    /**
     * Check in loaded family attributes.
     */
    public function checkInLoadedFamilyAttributes(): object
    {
        return core()->getSingletonInstance(AttributeRepository::class)
            ->getFamilyAttributes($this->attribute_family);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }
}
