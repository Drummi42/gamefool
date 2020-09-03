<?php

class GameFool
{
    private CardsDeck $deck;
    private array $players = [];
    private int $roundNum = 0;
    private int $attackerPID = 0;
    private int $defenderPID = 1;

    /**
     * @param object|null $object $object
     * @return $this|string
     * @throws Exception
     */
    public function __invoke(object $object = null): GameFool
    {
        // начало игры, если перестали приходить объекты
        if (is_null($object)) {
            $this->check();
            $this->game();
            return $this;
        }

        switch (get_class($object)) {
            case Player::class:
                /** @var Player $object */
                $this->addPlayer($object);
                break;
            case CardsDeck::class:
                /** @var CardsDeck $object */
                $this->setDeck($object);
                break;
        }
        return $this;
    }

    private function game(): void
    {
        $this->deck->firstDeal($this->players);

        $this->deck->writeLog();
        /** @var Player $player */
        foreach ($this->players as $player) {
            $player->writeLog();
            echo "\n";
        }

        while ($this->canPlay()) {
            $this->round();
        }

        echo empty($this->players) ? '-' : $this->players[0]->name;
    }

    private function round(): void
    {
        /**
         * @var Player $attacker
         * @var Player $defender
         */
        $attacker = $this->players[$this->attackerPID];
        $defender = $this->players[$this->defenderPID];
        $inGameCards = [];
        $failDefense = false;

        $this->roundNum++;
        echo "\n{$this->roundNum}: ";
        $attacker->writeLog();
        echo " vs ";
        $defender->writeLog();
        echo "\n";

        while (true) {
            $cardAttack = $attacker->attack($inGameCards);
            if (is_null($cardAttack)) {
                break;
            }
            $inGameCards[] = $cardAttack;
            echo "{$attacker->name} --> " . $cardAttack->getTypeName() . $cardAttack->getSuitName() . "\n";
            $cardDef = $defender->defend($cardAttack);
            if (is_null($cardDef)) {
                $failDefense = $defender->hasCards(); // если отбились, то защита успешная
                break;
            }
            $inGameCards[] = $cardDef;
            echo $cardDef->getTypeName() . $cardDef->getSuitName() . " <-- {$defender->name} \n";
        }

        if ($failDefense) {
            $discards = $attacker->discardSimilar($inGameCards); // докидка карт со стороны атаки
            $defender->takeLosingCards(array_merge($inGameCards, $discards));
        }
        $this->prepareNextRound($failDefense, $attacker, $defender);

        if (!$this->canPlay()) {
            return;
        }
    }

    private function addPlayer(Player $player): void
    {
        $this->players[] = $player;
    }

    private function removePlayer(int $index): void
    {
        unset($this->players[$index]);
    }

    private function setDeck(CardsDeck $deck): void
    {
        $this->deck = $deck;
    }

    private function check(): void
    {
        $playerNum = count($this->players);
        if ($playerNum < 2 || $playerNum > 4) {
            throw new Exception('Игроков может быть от 2 до 4', 400);
        }
    }

    // fixme: что-то сделать с определением хода.. не работает. Вообще пересмотреть эту хрень
    private function prepareNextRound(bool $pass, Player $attacker, Player $defender): void
    {
        $this->deck->dealCards($attacker);
        $this->deck->dealCards($defender);
        $attackerLeave = !$attacker->hasCards();
        $defenderLeave = !$defender->hasCards();

        if ($attackerLeave) {
            $this->removePlayer($this->attackerPID);
        }
        if ($defenderLeave) {
            $this->removePlayer($this->defenderPID);
        }
        if ($attackerLeave || $defenderLeave) {
            $this->players = array_values($this->players);
        }
        $numPlayers = count($this->players) - 1;

        $this->attackerPID = $this->defenderPID + ($pass ? 1 : 0) + ($defenderLeave ? 2 : 0);
        if ($this->attackerPID > $numPlayers) {
            $this->attackerPID %= $numPlayers + 1;
        }
        $this->defenderPID = $this->attackerPID + 1 + ($attackerLeave ? 1 : 0);
        if ($this->defenderPID > $numPlayers) {
            $this->defenderPID %= $numPlayers + 1;
        }
    }

    private function canPlay(): bool
    {
        return count($this->players) > 1;
    }
}
