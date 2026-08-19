import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

enum HealthMetricType {
  heartRate,
  bloodPressure,
  weight,
  bmi,
  steps,
  bloodOxygen,
  bloodGlucose,
  bodyTemp;

  String get displayName {
    switch (this) {
      case HealthMetricType.heartRate:
        return 'Heart Rate';
      case HealthMetricType.bloodPressure:
        return 'Blood Pressure';
      case HealthMetricType.weight:
        return 'Weight';
      case HealthMetricType.bmi:
        return 'BMI';
      case HealthMetricType.steps:
        return 'Daily Steps';
      case HealthMetricType.bloodOxygen:
        return 'Blood Oxygen (SpO2)';
      case HealthMetricType.bloodGlucose:
        return 'Blood Glucose';
      case HealthMetricType.bodyTemp:
        return 'Body Temperature';
    }
  }

  String get unit {
    switch (this) {
      case HealthMetricType.heartRate:
        return 'bpm';
      case HealthMetricType.bloodPressure:
        return 'mmHg';
      case HealthMetricType.weight:
        return 'kg';
      case HealthMetricType.bmi:
        return 'kg/m²';
      case HealthMetricType.steps:
        return 'steps';
      case HealthMetricType.bloodOxygen:
        return '%';
      case HealthMetricType.bloodGlucose:
        return 'mg/dL';
      case HealthMetricType.bodyTemp:
        return '°C';
    }
  }

  IconData get icon {
    switch (this) {
      case HealthMetricType.heartRate:
        return Icons.favorite_rounded;
      case HealthMetricType.bloodPressure:
        return Icons.speed_rounded;
      case HealthMetricType.weight:
        return Icons.monitor_weight_outlined;
      case HealthMetricType.bmi:
        return Icons.accessibility_new_rounded;
      case HealthMetricType.steps:
        return Icons.directions_walk_rounded;
      case HealthMetricType.bloodOxygen:
        return Icons.air_rounded;
      case HealthMetricType.bloodGlucose:
        return Icons.water_drop_outlined;
      case HealthMetricType.bodyTemp:
        return Icons.thermostat_rounded;
    }
  }

  Color get color {
    switch (this) {
      case HealthMetricType.heartRate:
        return AppColors.error;
      case HealthMetricType.bloodPressure:
        return AppColors.primary;
      case HealthMetricType.weight:
        return AppColors.secondary;
      case HealthMetricType.bmi:
        return const Color(0xFF8E44AD);
      case HealthMetricType.steps:
        return AppColors.success;
      case HealthMetricType.bloodOxygen:
        return const Color(0xFF2980B9);
      case HealthMetricType.bloodGlucose:
        return const Color(0xFFE67E22);
      case HealthMetricType.bodyTemp:
        return const Color(0xFFD35400);
    }
  }

  String toBackendString() {
    switch (this) {
      case HealthMetricType.heartRate:
        return 'heart_rate';
      case HealthMetricType.bloodPressure:
        return 'blood_pressure';
      case HealthMetricType.weight:
        return 'weight';
      case HealthMetricType.bmi:
        return 'bmi';
      case HealthMetricType.steps:
        return 'steps';
      case HealthMetricType.bloodOxygen:
        return 'blood_oxygen';
      case HealthMetricType.bloodGlucose:
        return 'blood_glucose';
      case HealthMetricType.bodyTemp:
        return 'body_temperature';
    }
  }

  static HealthMetricType fromBackendString(String? type) {
    switch (type?.toLowerCase()) {
      case 'heart_rate':
      case 'heartrate':
        return HealthMetricType.heartRate;
      case 'blood_pressure':
      case 'bloodpressure':
        return HealthMetricType.bloodPressure;
      case 'weight':
        return HealthMetricType.weight;
      case 'bmi':
        return HealthMetricType.bmi;
      case 'steps':
        return HealthMetricType.steps;
      case 'blood_oxygen':
      case 'bloodoxygen':
        return HealthMetricType.bloodOxygen;
      case 'blood_glucose':
      case 'bloodglucose':
        return HealthMetricType.bloodGlucose;
      case 'body_temperature':
      case 'bodytemp':
      case 'temperature':
        return HealthMetricType.bodyTemp;
      default:
        return HealthMetricType.heartRate;
    }
  }
}

class MetricReading {
  final DateTime timestamp;
  final double value;
  final String? secondaryValue; // e.g. "80" for 120/80 BP
  final String note;

  const MetricReading({
    required this.timestamp,
    required this.value,
    this.secondaryValue,
    this.note = 'Normal reading',
  });

  factory MetricReading.fromJson(Map<String, dynamic> json) {
    DateTime ts = DateTime.now();
    if (json['measured_at'] != null) {
      try {
        ts = DateTime.parse(json['measured_at'].toString());
      } catch (_) {}
    }

    return MetricReading(
      timestamp: ts,
      value: (json['value'] as num?)?.toDouble() ?? 0.0,
      secondaryValue: json['secondary_value']?.toString(),
      note: json['notes'] ?? 'Normal reading',
    );
  }
}

class HealthMetricModel {
  final HealthMetricType type;
  final String currentValue;
  final String statusLabel;
  final String trend; // e.g. "+2% from last week", "Stable"
  final DateTime lastUpdated;
  final List<MetricReading> history;

  const HealthMetricModel({
    required this.type,
    required this.currentValue,
    required this.statusLabel,
    required this.trend,
    required this.lastUpdated,
    required this.history,
  });
}
