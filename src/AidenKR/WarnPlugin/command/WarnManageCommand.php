<?php

namespace AidenKR\WarnPlugin\command;

use AidenKR\WarnPlugin\WarnPlugin;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;

class WarnManageCommand extends Command
{
    public function __construct(protected WarnPlugin $plugin)
    {
        parent::__construct("경고관리", "경고관리 명령어 입니다.");
        $this->setUsage("/{$this->getName()} [추가/회수] [닉네임] [횟수] [사유]");
        $this->setPermission(DefaultPermissions::ROOT_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof Player) {
            if (!isset($args[0]) or !isset($args[1]) or !isset($args[2]) or !is_numeric($args[2])) {
                $sender->sendMessage(WarnPlugin::$prefix . $this->getUsage());
                return;
            }

            switch ($args[0]) {
                case "추가":
                    $target = Server::getInstance()->getPlayerExact($args[1]);
                    $this->plugin->addWarn($sender, $target, $args[2], '관리자에 의한 경고 지급' ?? $args[3]);
                    break;


                case "회수":
                    $target = Server::getInstance()->getPlayerExact($args[1]);
                    $this->plugin->reduceWarn($sender, $target, $args[2]);
                    break;
            }
        }
    }
}
