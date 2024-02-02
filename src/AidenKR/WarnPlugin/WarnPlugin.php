<?php

namespace AidenKR\WarnPlugin;

use AidenKR\BandAPI\BandAPI;
use AidenKR\WarnPlugin\command\WarnCommand;
use AidenKR\WarnPlugin\command\WarnManageCommand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;

class WarnPlugin extends PluginBase
{
    use SingletonTrait;

    /** @var string */
    public static string $prefix = '§l§c[!] §r§7';

    /** @var array */
    protected array $players = [];

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getServer()->getCommandMap()->registerAll('AidenKR', [
            new WarnCommand($this), new WarnManageCommand($this)
        ]);

        $this->saveResource("players.json");
        $this->players = json_decode(file_get_contents($this->getDataFolder() . "players.json"), true);

    }

    protected function onDisable(): void
    {
        Filesystem::safeFilePutContents($this->getDataFolder() . "players.json", json_encode($this->players, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function createUserData(Player $player): void
    {
        $username = strtolower($player->getName());

        if (!isset($this->players[$username])) {
            $this->players[$username]["count"] = 0;
            $this->players[$username]["record"] = [];
        }
    }

    public function getWarnCount(Player $player): int
    {
        $username = strtolower($player->getName());
        return $this->players[$username]["count"] ?? 0;
    }

    public function getWarnRecord(Player $player): array
    {
        $username = strtolower($player->getName());
        return $this->players[$username]["record"] ?? [];
    }

    public function setWarnCount(Player $player, int $amount): void
    {
        $username = strtolower($player->getName());
        $this->players[$username]["count"] = $amount;
    }

    public function addWarn(Player $player, Player $target, int $count, string $reason = "관리자에 의한 경고지급"): void
    {
        $this->setWarnCount($target, $this->getWarnCount($player) + $count);
        $this->players[strtolower($target->getName())]["record"][] = [
            "date" => date(DATE_ATOM),
            "count" => $count,
            "reason" => $reason,
            "staff" => $player->getName()
        ];
        $this->getServer()->broadcastMessage(self::$prefix . "§a{$target->getName()}님§7에게 경고 §a{$count}회§7가 지급되었습니다. [처리자 : {$player->getName()}]");

        if ($this->getWarnCount($target) >= 5) {
            $this->getServer()->getNameBans()->addBan($target);

            if ($target->isOnline()) {
                $target->kick("서버이용 제한됨");
            }
        }
    }

    public function reduceWarn(Player $player, Player $target, int $count): void
    {
        $this->setWarnCount($target, $this->getWarnCount($player) - $count);
        $this->getServer()->broadcastMessage(self::$prefix . "§a{$target->getName()}님§7의 경고 §a{$count}회§7가 차감되었습니다. [처리자 : {$player->getName()}]");

        if ($this->getWarnCount($target) < 5) {
            $this->getServer()->getNameBans()->remove(strtolower($target->getName()));
            $this->getLogger()->warning("{$target->getName()}님의 경고가 5회 이하여서 서버 이용제한이 해제됨.");
        }
    }
}
