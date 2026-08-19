import 'package:flutter_test/flutter_test.dart';
import 'package:healthcare/features/health_tracker/data/repositories/mock_health_tracker_repository.dart';
import 'package:healthcare/features/health_tracker/domain/models/health_metric_model.dart';

void main() {
  late MockHealthTrackerRepository repository;

  setUp(() {
    repository = MockHealthTrackerRepository();
  });

  group('MockHealthTrackerRepository Tests', () {
    test('getMetrics returns 8 core health metrics', () async {
      final metrics = await repository.getMetrics();
      expect(metrics.length, 8);
    });

    test('getMetricByType returns specific metric', () async {
      final steps = await repository.getMetricByType(HealthMetricType.steps);
      expect(steps, isNotNull);
      expect(steps!.type, HealthMetricType.steps);
    });

    test('addReading adds new entry to metric history', () async {
      final initial = await repository.getMetricByType(HealthMetricType.heartRate);
      final initialCount = initial!.history.length;

      await repository.addReading(
        HealthMetricType.heartRate,
        78.0,
        note: 'After morning walk',
      );

      final updated = await repository.getMetricByType(HealthMetricType.heartRate);
      expect(updated!.currentValue, '78');
      expect(updated.history.length, initialCount + 1);
      expect(updated.history.first.value, 78.0);
    });
  });
}
