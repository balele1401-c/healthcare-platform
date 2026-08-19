import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_health_tracker_repository.dart';
import '../../domain/models/health_metric_model.dart';
import '../../domain/repositories/health_tracker_repository.dart';

final healthTrackerRepositoryProvider = Provider<HealthTrackerRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiHealthTrackerRepository(apiClient: apiClient);
});

final healthMetricsProvider = FutureProvider<List<HealthMetricModel>>((ref) async {
  final repository = ref.watch(healthTrackerRepositoryProvider);
  return repository.getMetrics();
});

final healthMetricDetailProvider = FutureProvider.family<HealthMetricModel?, HealthMetricType>((ref, type) async {
  final repository = ref.watch(healthTrackerRepositoryProvider);
  return repository.getMetricByType(type);
});
