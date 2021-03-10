<?php
namespace App\Entity;

use phpDocumentor\Reflection\Types\Boolean;

class PropertySearch {

    /**
     *@var Participants|null
     */
    private $organisateur;

    /**
     *@var Boolean|null
     */
    private $inscrit;

    /**
     * @return Boolean|null
     */
    public function getInscrit(): ?Boolean
    {
        return $this->inscrit;
    }

    /**
     * @return Participants|null
     */
    public function getOrganisateur(): ?Participants
    {
        return $this->organisateur;
    }

    /**
     * @param Boolean|null $inscrit
     */
    public function setInscrit(Boolean $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @param Participants|null $organisateur
     */
    public function setOrganisateur(Participants $organisateur): void
    {
        $this->organisateur = $organisateur;
    }
}