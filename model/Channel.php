<?php
/**
 * Data model for the "channel" database table
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.jemonweb.model
 */
class Channel extends DomainModel {

    private $realPower;
    private $apparentPower;
    private $powerFactor;
    private $vrms;
    private $irms;
    private $whInc;
    private $wh;
    private $phase;
    private $timestamp;

    public function __construct($realPower = null, $apparentPower = null, $powerFactor = null,
             $vrms = null, $irms = null, $whInc = null, $wh = null, $phase = null, $timestamp = null) {

        $this->realPower = $realPower;
        $this->apparentPower = $apparentPower;
        $this->powerFactor = $powerFactor;
        $this->vrms = $vrms;
        $this->irms = $irms;
        $this->whInc = $whInc;
        $this->wh = $wh;
        $this->phase = $phase;
        $this->timestamp = $timestamp;
    }

    public function setRealPower($realPower) {
        $this->realPower = $realPower;
    }

    public function getRealPower() {
        return $this->realPower;
    }

    public function setApparentPower($apparentPower) {
        $this->apparentPower = $apparentPower;
    }

    public function getApparentPower() {
        return $this->apparentPower;
    }

    public function setPowerFactor($powerFactor) {
        $this->powerFactor = $powerFactor;
    }

    public function getPowerFactor() {
        return $this->powerFactor;
    }

    public function setVrms($vrms) {
        $this->vrms = $vrms;
    }

    public function getVrms() {
        return $this->vrms;
    }

    public function setIrms($irms) {
        $this->irms = $irms;
    }

    public function getIrms() {
        return $this->irms;
    }

    public function setWhInc($whInc) {
        $this->whInc = $whInc;
    }

    public function getWhInc() {
        return $this->whInc;
    }

    public function setWh($wh) {
        $this->wh = $wh;
    }

    public function getWh() {
        return $this->wh;
    }
    
    public function setPhase($phase) {
        $this->phase = $phase;
    }

    public function getPhase() {
        return $this->phase;
    }
    
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }
}
?>