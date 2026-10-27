<?php

declare(strict_types=1);

namespace App\Controller;

final class TrainingController extends AppController
{
    private const USER = ['name' => 'nightowl', 'role' => 'USER'];

    public function index(): void
    {
        $this->render('training', [
            'pageTitle' => 'Training set',
            'activeNav' => 'training',
            'user' => self::USER,
            'mapName' => 'de_mirage',
            'demoId' => 1,
            'setName' => 'Mirage — Oct 6',
            'scenarios' => [
                ['you' => 'Top Mid', 'enemy' => 'Window', 'tags' => ['Crosshair 14° off', 'AWP angle']],
                ['you' => 'A Ramp', 'enemy' => 'A Site', 'tags' => ['Shooting while moving', 'Repeats 3× in 30 days']],
                ['you' => 'B Approach', 'enemy' => 'B Site', 'tags' => ['Crosshair 21° off', 'Off-angle']],
                ['you' => 'A Ramp', 'enemy' => 'A Site (R17)', 'tags' => ['Shooting while moving', 'Crosshair 9° off']],
            ],
            'cfgPreview' => <<<'CFG'
                // de_mirage · 4 scenarios · press N for the next one
                sv_cheats 1
                bot_kick
                bot_add_t 1
                bot_stop 1
                sv_infinite_ammo 1
                mp_roundtime 60

                // 1/4  Top Mid to Window — crosshair 14° off
                alias d1_s1 "setpos -358.0 -839.0 -60.0; setang 0.0 0.0; bot_place"
                CFG,
        ]);
    }
}
