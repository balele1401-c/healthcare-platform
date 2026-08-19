import 'package:flutter/material.dart';
import '../../../../core/theme/app_colors.dart';

enum NotificationCategory {
  appointment,
  message,
  prescription,
  reminder,
  system;

  String get label {
    switch (this) {
      case NotificationCategory.appointment:
        return 'Appointments';
      case NotificationCategory.message:
        return 'Messages';
      case NotificationCategory.prescription:
        return 'Prescriptions';
      case NotificationCategory.reminder:
        return 'Reminders';
      case NotificationCategory.system:
        return 'System';
    }
  }

  IconData get icon {
    switch (this) {
      case NotificationCategory.appointment:
        return Icons.calendar_month_rounded;
      case NotificationCategory.message:
        return Icons.chat_bubble_outline_rounded;
      case NotificationCategory.prescription:
        return Icons.medication_outlined;
      case NotificationCategory.reminder:
        return Icons.alarm_rounded;
      case NotificationCategory.system:
        return Icons.info_outline_rounded;
    }
  }

  Color get color {
    switch (this) {
      case NotificationCategory.appointment:
        return AppColors.primary;
      case NotificationCategory.message:
        return AppColors.secondary;
      case NotificationCategory.prescription:
        return AppColors.success;
      case NotificationCategory.reminder:
        return AppColors.warning;
      case NotificationCategory.system:
        return AppColors.outline;
    }
  }

  static NotificationCategory fromString(String? type) {
    switch (type?.toLowerCase()) {
      case 'appointment':
      case 'appointment_reminder':
      case 'appointment_created':
      case 'appointment_cancelled':
        return NotificationCategory.appointment;
      case 'message':
      case 'chat':
        return NotificationCategory.message;
      case 'prescription':
      case 'prescription_ready':
        return NotificationCategory.prescription;
      case 'reminder':
      case 'vital_alert':
        return NotificationCategory.reminder;
      default:
        return NotificationCategory.system;
    }
  }
}

class NotificationModel {
  final String id;
  final String title;
  final String description;
  final NotificationCategory category;
  final DateTime timestamp;
  final bool isRead;
  final String? routeTarget;

  const NotificationModel({
    required this.id,
    required this.title,
    required this.description,
    required this.category,
    required this.timestamp,
    this.isRead = false,
    this.routeTarget,
  });

  factory NotificationModel.fromJson(Map<String, dynamic> json) {
    DateTime ts = DateTime.now();
    if (json['created_at'] != null) {
      try {
        ts = DateTime.parse(json['created_at'].toString());
      } catch (_) {}
    }

    return NotificationModel(
      id: json['id']?.toString() ?? '',
      title: json['title'] ?? 'HealthCare Alert',
      description: json['message'] ?? '',
      category: NotificationCategory.fromString(json['notification_type']?.toString()),
      timestamp: ts,
      isRead: json['is_read'] == true || json['read_at'] != null,
      routeTarget: json['route_target'],
    );
  }

  NotificationModel copyWith({
    String? id,
    String? title,
    String? description,
    NotificationCategory? category,
    DateTime? timestamp,
    bool? isRead,
    String? routeTarget,
  }) {
    return NotificationModel(
      id: id ?? this.id,
      title: title ?? this.title,
      description: description ?? this.description,
      category: category ?? this.category,
      timestamp: timestamp ?? this.timestamp,
      isRead: isRead ?? this.isRead,
      routeTarget: routeTarget ?? this.routeTarget,
    );
  }
}
