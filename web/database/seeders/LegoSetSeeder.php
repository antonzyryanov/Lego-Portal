<?php

namespace Database\Seeders;

use App\Models\LegoSet;
use App\Models\Series;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegoSetSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Harry Potter' => [
                ['4709', 'Hogwarts Castle', '2001-01-01', 89.99, 'The first LEGO Hogwarts Castle with modular towers, classrooms, and Harry Potter minifigures.'],
                ['4714', 'Gringotts Bank', '2002-01-01', 49.99, 'Visit the wizarding bank with carts, vault doors, and goblin staff ready for business.'],
                ['4757', 'Hogwarts Castle', '2004-01-01', 69.99, 'An updated Hogwarts build featuring the Great Hall and more castle rooms to explore.'],
                ['4766', 'Graveyard Duel', '2005-01-01', 29.99, 'Recreate the cemetery confrontation with tombstones, a cauldron, and duel-ready wizards.'],
                ['5378', 'Hogwarts Castle', '2007-01-01', 129.99, 'A larger Hogwarts Castle with multiple towers, interiors, and classic film scenes.'],
                ['4841', 'Hogwarts Express', '2010-01-01', 79.99, 'Ride the Hogwarts Express with a detailed locomotive, carriages, and Platform 9¾.'],
                ['4842', 'Hogwarts Castle', '2010-01-01', 129.99, 'A landmark Hogwarts Castle set packed with rooms, courtyards, and minifigure moments.'],
                ['4867', 'Hogwarts', '2011-01-01', 49.99, 'A compact Hogwarts build capturing key castle sections and student life scenes.'],
                ['4736', 'Freeing Dobby', '2010-01-01', 16.99, 'Help free Dobby with sock-tossing fun and a memorable Chamber of Secrets moment.'],
                ['4738', "Hagrid's Hut", '2010-01-01', 39.99, "Visit Hagrid's cozy hut with pumpkin patch details and forest creature friends."],
            ],
            'Star Wars' => [
                ['7140', 'X-Wing Fighter', '1999-01-01', 34.99, 'The classic Rebel X-Wing with opening wings and Luke ready for trench-run action.'],
                ['7150', 'TIE Fighter & Y-Wing', '1999-01-01', 49.99, 'A dogfight duo featuring an Imperial TIE Fighter and a Rebel Y-Wing bomber.'],
                ['7190', 'Millennium Falcon', '2000-01-01', 149.99, 'An early Millennium Falcon playset with iconic saucer shape and smuggler crew.'],
                ['4504', 'Millennium Falcon', '2004-01-01', 99.99, 'A refreshed Falcon build with more play features for hyperspace adventures.'],
                ['10143', 'Death Star II UCS', '2005-01-01', 269.99, 'Ultimate Collector Series Death Star II display model for serious Star Wars fans.'],
                ['7261', 'Clone Turbo Tank', '2005-01-01', 99.99, 'Roll out Republic armor with the massive Clone Turbo Tank and clone troopers.'],
                ['6211', 'Imperial Star Destroyer', '2006-01-01', 99.99, 'Command the Imperial fleet with a play-scale Star Destroyer packed with details.'],
                ['7662', 'Trade Federation MTT', '2007-01-01', 49.99, 'Deploy battle droids from the Trade Federation Multi-Troop Transport.'],
                ['10179', "Ultimate Collector's Millennium Falcon", '2007-01-01', 499.99, 'The legendary UCS Millennium Falcon — a massive display centerpiece for collectors.'],
                ['7672', 'Rogue Shadow', '2008-01-01', 79.99, 'The stealthy Rogue Shadow starship from The Force Unleashed era.'],
            ],
            'Indiana Jones' => [
                ['7620', 'Motorcycle Chase', '2008-01-01', 19.99, 'Escape on motorcycles in a high-speed chase packed with adventure action.'],
                ['7621', 'The Lost Tomb', '2008-01-01', 29.99, 'Explore a trapped tomb filled with treasure, snakes, and classic Indy peril.'],
                ['7622', 'Race for the Stolen Treasure', '2008-01-01', 49.99, 'Race through the desert to recover stolen artifacts from enemy forces.'],
                ['7623', 'Temple Escape', '2008-01-01', 59.99, 'Survive temple traps and make a daring escape with the golden idol.'],
                ['7624', 'Jungle Duel', '2008-01-01', 19.99, 'A jungle showdown with vines, ruins, and sword-clashing adventure.'],
                ['7625', 'River Chase', '2008-01-01', 39.99, 'Navigate a river boat chase through dangerous waters and enemy ambushes.'],
                ['7626', 'Jungle Cutter', '2008-01-01', 49.99, 'Cut through dense jungle with a rugged vehicle built for expedition work.'],
                ['7627', 'Temple of the Crystal Skull', '2008-01-01', 89.99, 'Discover the Temple of the Crystal Skull with detailed ruins and story scenes.'],
                ['7628', 'Peril in Peru', '2009-01-01', 59.99, 'Begin the Crystal Skull adventure in Peru with warehouse and chase action.'],
                ['7199', 'The Temple of Doom', '2009-01-01', 99.99, 'Enter the Temple of Doom with mine carts, traps, and thrilling set pieces.'],
                ['7683', 'Fight on the Flying Wing', '2009-01-01', 49.99, 'Battle atop a flying wing aircraft in a Raiders of the Lost Ark showdown.'],
            ],
            'Batman' => [
                ['7780', 'The Batboat: Hunt for Killer Croc', '2006-01-01', 29.99, 'Chase Killer Croc across the water in the agile Batboat.'],
                ['7781', "The Batmobile: Two-Face's Escape", '2006-01-01', 49.99, "Stop Two-Face's escape with the classic Batmobile and bank heist action."],
                ['7782', "The Batwing: Joker's Aerial Assault", '2006-01-01', 49.99, "Take to the skies in the Batwing against the Joker's aerial chaos."],
                ['7783', 'The Batcave: Penguin and Mr. Freeze', '2006-01-01', 149.99, 'Defend the Batcave from Penguin and Mr. Freeze with vehicles and gadgets.'],
                ['7784', 'The Batmobile: UCS', '2006-01-01', 199.99, 'Ultimate Collector Series Batmobile display model with sleek Gotham styling.'],
                ['7785', 'Arkham Asylum', '2006-01-01', 89.99, 'Contain Gotham villains inside the infamous Arkham Asylum complex.'],
                ['7786', 'The Batcopter: The Chase for Scarecrow', '2007-01-01', 39.99, 'Hunt Scarecrow from the air with the Batcopter and fear-toxin intrigue.'],
                ['7787', 'The Bat-Tank: The Riddler and Bane', '2007-01-01', 59.99, 'Roll out the Bat-Tank to confront Riddler and Bane in armored combat.'],
                ['7884', "Batman's Buggy: The Escape of Mr. Freeze", '2008-01-01', 19.99, "Pursue Mr. Freeze in Batman's compact buggy through icy streets."],
                ['7885', "Robin's Scuba Jet: Attack of The Penguin", '2008-01-01', 24.99, "Help Robin stop The Penguin's underwater assault with the Scuba Jet."],
                ['7886', "The Batcycle: Harley Quinn's Challenge", '2008-01-01', 24.99, "Race the Batcycle against Harley Quinn's chaotic challenge."],
                ['7888', "The Tumbler: Joker's Ice Cream Surprise", '2008-01-01', 69.99, "Deploy the Tumbler against the Joker's infamous ice cream truck scheme."],
            ],
        ];

        foreach ($catalog as $seriesName => $sets) {
            $series = Series::query()->where('slug', Str::slug($seriesName))->firstOrFail();

            foreach ($sets as [$article, $name, $releaseDate, $price, $description]) {
                LegoSet::query()->updateOrCreate(
                    ['article_number' => $article],
                    [
                        'series_id' => $series->id,
                        'name' => $name,
                        'description' => $description,
                        'original_price' => $price,
                        'release_date' => $releaseDate,
                        'image_path' => 'https://cdn.rebrickable.com/media/sets/'.$article.'-1.jpg',
                    ],
                );
            }
        }
    }
}
