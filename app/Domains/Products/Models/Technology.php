<?php

namespace App\Domains\Products\Models;

use App\Domains\Portfolio\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Shared technology taxonomy (products ↔ portfolio).
 *
 * Seeded with a starter catalog (see Database\Seeders\TechnologySeeder) but
 * never hardcoded in domain code — operators manage the catalog in Blue
 * Control.
 */
class Technology extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    public function portfolioProjects(): BelongsToMany
    {
        return $this->belongsToMany(PortfolioProject::class, 'portfolio_project_technology');
    }
}
