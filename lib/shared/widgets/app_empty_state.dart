import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_spacing.dart';
import '../../core/theme/app_typography.dart';
import 'app_button.dart';

class AppEmptyState extends StatelessWidget {
  final IconData icon;
  final String title;
  final String message;
  final String? actionText;
  final VoidCallback? onAction;

  const AppEmptyState({
    super.key,
    this.icon = Icons.inbox_rounded,
    required this.title,
    required this.message,
    this.actionText,
    this.onAction,
  });

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: AppSpacing.paddingScreenAll,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: AppColors.primaryContainer.withValues(alpha: 0.3),
                shape: BoxShape.circle,
              ),
              child: Icon(
                icon,
                color: AppColors.primary,
                size: 40,
              ),
            ),
            AppSpacing.gapVMd,
            Text(
              title,
              style: AppTypography.headlineSm.copyWith(color: AppColors.onSurface),
              textAlign: TextAlign.center,
            ),
            AppSpacing.gapVSm,
            Text(
              message,
              style: AppTypography.bodyMd.copyWith(color: AppColors.onSurfaceVariant),
              textAlign: TextAlign.center,
            ),
            if (actionText != null && onAction != null) ...[
              AppSpacing.gapVLg,
              AppButton(
                text: actionText!,
                onPressed: onAction,
                isFullWidth: false,
                variant: ButtonVariant.outlined,
              ),
            ],
          ],
        ),
      ),
    );
  }
}
