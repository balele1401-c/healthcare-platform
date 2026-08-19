import '../../domain/models/notification_model.dart';
import '../../domain/repositories/notification_repository.dart';
import '../../../../core/routing/app_routes.dart';

class MockNotificationRepository implements NotificationRepository {
  static final List<NotificationModel> _notifications = [
    NotificationModel(
      id: 'NOTIF-001',
      title: 'Upcoming Appointment in 3 Hours',
      description: 'Your video consultation with Dr. Emily Chen is scheduled for today at 10:30 AM.',
      category: NotificationCategory.appointment,
      timestamp: DateTime.now().subtract(const Duration(minutes: 15)),
      isRead: false,
      routeTarget: AppRoutes.appointmentDetail,
    ),
    NotificationModel(
      id: 'NOTIF-002',
      title: 'Dr. Marcus Vance sent you a message',
      description: '"Please remember to take the skin photograph before starting the cream."',
      category: NotificationCategory.message,
      timestamp: DateTime.now().subtract(const Duration(hours: 2)),
      isRead: false,
      routeTarget: AppRoutes.chat,
    ),
    NotificationModel(
      id: 'NOTIF-003',
      title: 'Prescription Refill Ready',
      description: 'Your prescription for Amlodipine Besylate 5mg has been updated by Dr. Emily Chen.',
      category: NotificationCategory.prescription,
      timestamp: DateTime.now().subtract(const Duration(days: 1)),
      isRead: true,
      routeTarget: AppRoutes.prescriptions,
    ),
    NotificationModel(
      id: 'NOTIF-004',
      title: 'Daily Health Reminder',
      description: 'Time to record your evening blood pressure and daily step count.',
      category: NotificationCategory.reminder,
      timestamp: DateTime.now().subtract(const Duration(days: 1, hours: 4)),
      isRead: true,
      routeTarget: AppRoutes.healthTracker,
    ),
    NotificationModel(
      id: 'NOTIF-005',
      title: 'System Security Update',
      description: 'Two-Factor Authentication is now enabled for your patient account.',
      category: NotificationCategory.system,
      timestamp: DateTime.now().subtract(const Duration(days: 3)),
      isRead: true,
    ),
  ];

  @override
  Future<List<NotificationModel>> getNotifications({NotificationCategory? category}) async {
    await Future.delayed(const Duration(milliseconds: 200));
    if (category == null) {
      return List.unmodifiable(_notifications);
    }
    return _notifications.where((n) => n.category == category).toList();
  }

  @override
  Future<int> getUnreadCount() async {
    await Future.delayed(const Duration(milliseconds: 100));
    return _notifications.where((n) => !n.isRead).length;
  }

  @override
  Future<void> markAsRead(String id) async {
    await Future.delayed(const Duration(milliseconds: 150));
    final index = _notifications.indexWhere((n) => n.id == id);
    if (index != -1) {
      _notifications[index] = _notifications[index].copyWith(isRead: true);
    }
  }

  @override
  Future<void> markAllAsRead() async {
    await Future.delayed(const Duration(milliseconds: 200));
    for (int i = 0; i < _notifications.length; i++) {
      _notifications[i] = _notifications[i].copyWith(isRead: true);
    }
  }
}
