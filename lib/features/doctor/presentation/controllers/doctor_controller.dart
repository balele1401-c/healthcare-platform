import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_doctor_repository.dart';
import '../../domain/models/doctor_model.dart';
import '../../domain/repositories/doctor_repository.dart';

final doctorRepositoryProvider = Provider<DoctorRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiDoctorRepository(apiClient: apiClient);
});

class DoctorFilterState {
  final String searchQuery;
  final String selectedSpecialtyId;
  final double minRating;
  final double maxFee;
  final bool onlyAvailableToday;

  const DoctorFilterState({
    this.searchQuery = '',
    this.selectedSpecialtyId = 'all',
    this.minRating = 0.0,
    this.maxFee = 150.0,
    this.onlyAvailableToday = false,
  });

  DoctorFilterState copyWith({
    String? searchQuery,
    String? selectedSpecialtyId,
    double? minRating,
    double? maxFee,
    bool? onlyAvailableToday,
  }) {
    return DoctorFilterState(
      searchQuery: searchQuery ?? this.searchQuery,
      selectedSpecialtyId: selectedSpecialtyId ?? this.selectedSpecialtyId,
      minRating: minRating ?? this.minRating,
      maxFee: maxFee ?? this.maxFee,
      onlyAvailableToday: onlyAvailableToday ?? this.onlyAvailableToday,
    );
  }
}

final doctorFilterProvider = StateNotifierProvider<DoctorFilterNotifier, DoctorFilterState>((ref) {
  return DoctorFilterNotifier();
});

class DoctorFilterNotifier extends StateNotifier<DoctorFilterState> {
  DoctorFilterNotifier() : super(const DoctorFilterState());

  void setSearchQuery(String query) {
    state = state.copyWith(searchQuery: query);
  }

  void selectSpecialty(String specialtyId) {
    state = state.copyWith(selectedSpecialtyId: specialtyId);
  }

  void setMinRating(double rating) {
    state = state.copyWith(minRating: rating);
  }

  void setMaxFee(double fee) {
    state = state.copyWith(maxFee: fee);
  }

  void toggleAvailableToday(bool val) {
    state = state.copyWith(onlyAvailableToday: val);
  }

  void resetFilters() {
    state = DoctorFilterState(searchQuery: state.searchQuery);
  }
}

final doctorListProvider = FutureProvider<List<DoctorModel>>((ref) async {
  final repository = ref.watch(doctorRepositoryProvider);
  final filter = ref.watch(doctorFilterProvider);

  return repository.getDoctors(
    searchQuery: filter.searchQuery,
    specialtyId: filter.selectedSpecialtyId,
    minRating: filter.minRating,
    maxFee: filter.maxFee,
    onlyAvailableToday: filter.onlyAvailableToday,
  );
});

final recommendedDoctorsProvider = FutureProvider<List<DoctorModel>>((ref) async {
  final repository = ref.watch(doctorRepositoryProvider);
  return repository.getRecommendedDoctors();
});

final specialtiesProvider = FutureProvider<List<SpecialtyModel>>((ref) async {
  final repository = ref.watch(doctorRepositoryProvider);
  return repository.getSpecialties();
});

final doctorDetailProvider = FutureProvider.family<DoctorModel?, String>((ref, id) async {
  final repository = ref.watch(doctorRepositoryProvider);
  return repository.getDoctorById(id);
});
