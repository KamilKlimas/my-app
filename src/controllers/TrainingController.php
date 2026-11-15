<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\CurrentUser;
use App\View\View;

final class TrainingController extends AppController
{
    public function __construct(
        private readonly View $view,
        private readonly CurrentUser $currentUser,
    ) {
    }

    public function index(): void
    {
        $this->view->render('training', [
            'pageTitle' => 'training.title',
            'activeNav' => 'training',
            'user' => $this->currentUser->user(),
            'mapName' => 'de_mirage',
            'demoId' => 1,
            'setName' => 'Mirage — Oct 6',
            'scenarios' => [
                ['you' => 'Top Mid', 'enemy' => 'Window', 'tags' => [['crosshair_off', ['degrees' => 14]], ['awp_angle', []]]],
                ['you' => 'A Ramp', 'enemy' => 'A Site', 'tags' => [['moving', []], ['repeats', ['count' => 3, 'days' => 30]]]],
                ['you' => 'B Approach', 'enemy' => 'B Site', 'tags' => [['crosshair_off', ['degrees' => 21]], ['off_angle', []]]],
                ['you' => 'A Ramp', 'enemy' => 'A Site (R17)', 'tags' => [['moving', []], ['crosshair_off', ['degrees' => 9]]]],
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
