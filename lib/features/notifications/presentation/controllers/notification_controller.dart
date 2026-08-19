import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/network/api_client.dart';
import '../../data/repositories/api_notification_repository.dart';
import '../../domain/models/notification_model.dart';
import '../../domain/repositories/notification_repository.dart';

final notificationRepositoryProvider = Provider<NotificationRepository>((ref) {
  final apiClient = ref.watch(apiClientProvider);
  return ApiNotificationRepository(apiClient: apiClient);
});

final selectedNotificationCategoryProvider = StateProvider<NotificationCategory?>((ref) => null);

final notificationsProvider = FutureProvider<List<NotificationModel>>((ref) async {
  final repository = ref.watch(notificationRepositoryProvider);
  final category = ref.watch(selectedNotificationCategoryProvider);
  return repository.getNotifications(category: category);
});

final unreadNotificationCountProvider = FutureProvider<int>((ref) async {
  final repository = ref.watch(notificationRepositoryProvider);
  return repository.getUnreadCount();
});
