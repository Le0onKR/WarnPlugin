<?php

namespace AidenKR\WarnPlugin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;

class EventListener implements Listener
{
    public function __construct(
        protected WarnPlugin $plugin
    ) {}

    public function onJoin(PlayerJoinEvent $event): void
    {
        $this->plugin->createUserData($event->getPlayer());
    }

    public function onLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();

        $this->plugin->createUserData($player);

        if ($this->plugin->getWarnCount($player) >= 10) {
            $event->cancel();
            $player->kick("서버이용이 제한되어있습니다.");
        }
    }
}
