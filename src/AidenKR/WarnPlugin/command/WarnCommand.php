<?php

namespace AidenKR\WarnPlugin\command;

use AidenKR\WarnPlugin\form\WarnMainForm;
use AidenKR\WarnPlugin\WarnPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class WarnCommand extends Command
{
    public function __construct(protected WarnPlugin $plugin)
    {
        parent::__construct("내경고", "나의 경고 횟수를 확인합니다.");
        $this->setPermission(DefaultPermissions::ROOT_USER);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            $sender->sendForm(new WarnMainForm($this->plugin));
        }
    }
}
