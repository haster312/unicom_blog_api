<?php

namespace App\Console\Commands;

use App\Repositories\CategoryRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public $categoryRepo;
    public function __construct(CategoryRepo $categoryRepo)
    {
        parent::__construct();
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categories = [
            "Arts & Social Science", "Business & Commerce", "Education & Teaching", "Health",
            "IT & Engineering","Law & Criminology","Science",
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category);
            $this->categoryRepo->create([
                'name' => $category,
                'slug' => $slug
            ]);
        }
    }
}
