<?php

namespace App\Enums;

enum HealthMetricType: string
{
    case WEIGHT = 'weight';
    case HEIGHT = 'height';
    case BMI = 'bmi';
    case BLOOD_PRESSURE = 'blood_pressure';
    case HEART_RATE = 'heart_rate';
    case BLOOD_OXYGEN = 'blood_oxygen';
    case BLOOD_GLUCOSE = 'blood_glucose';
    case BODY_TEMPERATURE = 'body_temperature';

    public function label(): string
    {
        return match ($this) {
            self::WEIGHT => 'Body Weight',
            self::HEIGHT => 'Height',
            self::BMI => 'Body Mass Index',
            self::BLOOD_PRESSURE => 'Blood Pressure',
            self::HEART_RATE => 'Heart Rate',
            self::BLOOD_OXYGEN => 'Blood Oxygen (SpO2)',
            self::BLOOD_GLUCOSE => 'Blood Glucose',
            self::BODY_TEMPERATURE => 'Body Temperature',
        };
    }

    public function defaultUnit(): string
    {
        return match ($this) {
            self::WEIGHT => 'kg',
            self::HEIGHT => 'cm',
            self::BMI => 'kg/m²',
            self::BLOOD_PRESSURE => 'mmHg',
            self::HEART_RATE => 'bpm',
            self::BLOOD_OXYGEN => '%',
            self::BLOOD_GLUCOSE => 'mg/dL',
            self::BODY_TEMPERATURE => '°C',
        };
    }
}
