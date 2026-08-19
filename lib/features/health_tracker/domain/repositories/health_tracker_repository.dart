import '../models/health_metric_model.dart';

abstract class HealthTrackerRepository {
  Future<List<HealthMetricModel>> getMetrics();

  Future<HealthMetricModel?> getMetricByType(HealthMetricType type);

  Future<void> addReading(
    HealthMetricType type,
    double value, {
    String? secondaryValue,
    String? note,
  });
}
