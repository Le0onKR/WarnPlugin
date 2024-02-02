<?php

namespace AidenKR\WarnPlugin\form;

use AidenKR\WarnPlugin\WarnPlugin;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\Server;

class WarnMainForm implements Form
{
    public function __construct(
        protected WarnPlugin $plugin
    ) {}

    protected function getContent(): string
    {
        $c = PHP_EOL;
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $c .= "§r§6* §r§f{$player->getName()}님의 총 경고수 : " . $this->plugin->getWarnCount($player) . "회";
            $c .= str_repeat(PHP_EOL, 2);
        }
        return $c;
    }

    protected function addButton(): array
    {
        $btn = [];

        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            foreach ($this->plugin->getWarnRecord($player) as $data) {
                $btn[] = ["text" => "§l{$data["date"]}\n§r§0- 사유 : {$data["reason"]} | 횟수 : {$data["count"]} -"];
            }
        }
        return $btn;
    }

    public function jsonSerialize(): mixed
    {
        return [
            "type" => "form",
            "title" => "§l나의 경고 기록",
            "content" => $this->getContent(),
            "buttons" => $this->addButton()
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
    }
}
