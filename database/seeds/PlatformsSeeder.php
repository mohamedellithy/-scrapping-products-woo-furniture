<?php

use Illuminate\Database\Seeder;
use App\FurniturePlatform;
class PlatformsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $platforms = [
            "ACE",
            "Alhuzaifa",
            "DanubeHome",
            "Dwell",
            "Ebarza",
            "HomeBox",
            "HomeCentre",
            "Ikea",
            "MarinaHomeInteriors",
            "Mujj",
            "PanEmirates",
            "RoyalFurniture",
            "TheHome",
            "WallSnation",
            "WestElm",
            "WoodentWist"
        ];

        foreach($platforms as $platform):
            $insert_platforms = FurniturePlatform::UpdateorCreate(["name" => $platform]);
        endforeach;


    }
}
