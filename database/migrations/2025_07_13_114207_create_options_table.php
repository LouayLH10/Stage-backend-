<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->string('option');
            $table->string('icon');
            $table->timestamps();
        });
            DB::table('options')->insert([
            ['option' => 'Camera de surveillance',
            'icon'=>'options/default_icons/camera.png'
            ] ,
             ['option' => 'Chauffage centrale',
            'icon'=>'options/default_icons/chaufage.png'
            ],
              ['option' => 'Climatiseur',
            'icon'=>'options/default_icons/climatiseur.png'
        ],
          ['option' => 'Picine Couverte',
            'icon'=>'options/default_icons/covert.png'
    ],
        ['option' => 'Armoire agence',
            'icon'=>'options/default_icons/camera.png'
],
  ['option' => 'Assenceur',
            'icon'=>'options/default_icons/elevator.png'
],
  ['option' => 'Salle de sport equipé',
            'icon'=>'options/default_icons/gym.png'
            ],
  ['option' => 'Jardin privé',
            'icon'=>'options/default_icons/jardin.png'
        ],
          ['option' => 'Parking privé',
            'icon'=>'options/default_icons/parking.png'
    ],
      ['option' => 'Sauna moderne et jaccusie',
            'icon'=>'options/default_icons/camera.png'
],
  ['option' => 'Picine non-couvert',
            'icon'=>'options/default_icons/pool.png'
],
  ['option' => 'Smart House',
            'icon'=>'options/default_icons/smart.png'
],
  ['option' => 'WIFI',
            'icon'=>'options/default_icons/wifi.png'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
