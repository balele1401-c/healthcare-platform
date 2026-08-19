import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_prescription_repository.dart';
import '../../domain/models/prescription_model.dart';
import '../../domain/repositories/prescription_repository.dart';

final prescriptionRepositoryProvider = Provider<PrescriptionRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiPrescriptionRepository(apiClient: apiClient);
});

final activePrescriptionsProvider = FutureProvider<List<PrescriptionModel>>((ref) async {
  final repository = ref.watch(prescriptionRepositoryProvider);
  return repository.getPrescriptions(status: PrescriptionStatus.active);
});

final completedPrescriptionsProvider = FutureProvider<List<PrescriptionModel>>((ref) async {
  final repository = ref.watch(prescriptionRepositoryProvider);
  return repository.getPrescriptions(status: PrescriptionStatus.completed);
});

final expiredPrescriptionsProvider = FutureProvider<List<PrescriptionModel>>((ref) async {
  final repository = ref.watch(prescriptionRepositoryProvider);
  return repository.getPrescriptions(status: PrescriptionStatus.expired);
});

final prescriptionDetailProvider = FutureProvider.family<PrescriptionModel?, String>((ref, id) async {
  final repository = ref.watch(prescriptionRepositoryProvider);
  return repository.getPrescriptionById(id);
});
