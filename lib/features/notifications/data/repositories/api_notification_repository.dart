import '../../../../core/network/api_client.dart';
import '../../domain/models/notification_model.dart';
import '../../domain/repositories/notification_repository.dart';

class ApiNotificationRepository implements NotificationRepository {
  final ApiClient _apiClient;

  ApiNotificationRepository({required ApiClient apiClient}) : _apiClient = apiClient;

  @override
  Future<List<NotificationModel>> getNotifications({NotificationCategory? category}) async {
    final response = await _apiClient.get('/notifications');
    final data = response.data['data'] as List? ?? [];

    var list = data
        .map((item) => NotificationModel.fromJson(item as Map<String, dynamic>))
        .toList();

    if (category != null) {
      list = list.where((n) => n.category == category).toList();
    }

    return list;
  }

  @override
  Future<int> getUnreadCount() async {
    try {
      final response = await _apiClient.get('/notifications', queryParameters: {'read': 0});
      final data = response.data['data'] as List? ?? [];
      return data.length;
    } catch (_) {
      return 0;
    }
  }

  @override
  Future<void> markAsRead(String id) async {
    try {
      await _apiClient.post('/notifications/$id/read');
    } catch (_) {}
  }

  @override
  Future<void> markAllAsRead() async {
    try {
      await _apiClient.post('/notifications/read-all');
    } catch (_) {}
  }
}
