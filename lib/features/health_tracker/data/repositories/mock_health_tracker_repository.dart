import '../../domain/models/health_metric_model.dart';
import '../../domain/repositories/health_tracker_repository.dart';

class MockHealthTrackerRepository implements HealthTrackerRepository {
  static final List<HealthMetricModel> _metrics = [
    HealthMetricModel(
      type: HealthMetricType.steps,
      currentValue: '8,432',
      statusLabel: 'Goal: 10,000',
      trend: '+12% vs yesterday',
      lastUpdated: DateTime.now().subtract(const Duration(minutes: 25)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 6)), value: 7200),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 5)), value: 8100),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 4)), value: 6900),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 3)), value: 9400),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 2)), value: 10200),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 1)), value: 7500),
        MetricReading(timestamp: DateTime.now(), value: 8432),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.heartRate,
      currentValue: '72',
      statusLabel: 'Resting • Normal',
      trend: 'Avg 68-74 bpm this week',
      lastUpdated: DateTime.now().subtract(const Duration(hours: 1)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 6)), value: 70),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 5)), value: 74),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 4)), value: 68),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 3)), value: 71),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 2)), value: 75),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 1)), value: 69),
        MetricReading(timestamp: DateTime.now(), value: 72),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.bloodPressure,
      currentValue: '118/76',
      statusLabel: 'Optimal Range',
      trend: 'Stable over 30 days',
      lastUpdated: DateTime.now().subtract(const Duration(hours: 3)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 6)), value: 120, secondaryValue: '78'),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 4)), value: 122, secondaryValue: '80'),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 2)), value: 116, secondaryValue: '75'),
        MetricReading(timestamp: DateTime.now(), value: 118, secondaryValue: '76'),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.weight,
      currentValue: '64.5',
      statusLabel: 'Target: 62.0 kg',
      trend: '-0.8 kg this month',
      lastUpdated: DateTime.now().subtract(const Duration(days: 1)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 30)), value: 65.3),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 20)), value: 65.0),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 10)), value: 64.8),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 1)), value: 64.5),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.bmi,
      currentValue: '22.9',
      statusLabel: 'Normal Weight',
      trend: 'Healthy range (18.5 - 24.9)',
      lastUpdated: DateTime.now().subtract(const Duration(days: 1)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 30)), value: 23.1),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 1)), value: 22.9),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.bloodOxygen,
      currentValue: '99',
      statusLabel: 'Excellent',
      trend: 'Normal range ≥95%',
      lastUpdated: DateTime.now().subtract(const Duration(hours: 2)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 3)), value: 98),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 1)), value: 99),
        MetricReading(timestamp: DateTime.now(), value: 99),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.bloodGlucose,
      currentValue: '92',
      statusLabel: 'Fasting • Normal',
      trend: 'Target: 70-99 mg/dL',
      lastUpdated: DateTime.now().subtract(const Duration(hours: 5)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 7)), value: 95),
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 3)), value: 89),
        MetricReading(timestamp: DateTime.now(), value: 92),
      ],
    ),
    HealthMetricModel(
      type: HealthMetricType.bodyTemp,
      currentValue: '36.6',
      statusLabel: 'Afebrile',
      trend: 'Normal (36.1 - 37.2 °C)',
      lastUpdated: DateTime.now().subtract(const Duration(hours: 4)),
      history: [
        MetricReading(timestamp: DateTime.now().subtract(const Duration(days: 2)), value: 36.5),
        MetricReading(timestamp: DateTime.now(), value: 36.6),
      ],
    ),
  ];

  @override
  Future<List<HealthMetricModel>> getMetrics() async {
    await Future.delayed(const Duration(milliseconds: 250));
    return List.unmodifiable(_metrics);
  }

  @override
  Future<HealthMetricModel?> getMetricByType(HealthMetricType type) async {
    await Future.delayed(const Duration(milliseconds: 150));
    try {
      return _metrics.firstWhere((m) => m.type == type);
    } catch (_) {
      return _metrics.first;
    }
  }

  @override
  Future<void> addReading(HealthMetricType type, double value, {String? secondaryValue, String? note}) async {
    await Future.delayed(const Duration(milliseconds: 300));
    final index = _metrics.indexWhere((m) => m.type == type);
    if (index != -1) {
      final current = _metrics[index];
      final newHistory = List<MetricReading>.from(current.history)
        ..insert(
          0,
          MetricReading(
            timestamp: DateTime.now(),
            value: value,
            secondaryValue: secondaryValue,
            note: note ?? 'User recorded reading',
          ),
        );

      final newValueString = secondaryValue != null
          ? '${value.toInt()}/$secondaryValue'
          : value == value.roundToDouble()
              ? value.toInt().toString()
              : value.toStringAsFixed(1);

      _metrics[index] = HealthMetricModel(
        type: current.type,
        currentValue: newValueString,
        statusLabel: current.statusLabel,
        trend: 'Updated just now',
        lastUpdated: DateTime.now(),
        history: newHistory,
      );
    }
  }
}
