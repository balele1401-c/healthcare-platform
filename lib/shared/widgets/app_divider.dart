import 'package:flutter/material.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_spacing.dart';
import '../../core/theme/app_typography.dart';

class AppDivider extends StatelessWidget {
  final String? text;
  final double verticalPadding;

  const AppDivider({
    super.key,
    this.text,
    this.verticalPadding = AppSpacing.lg,
  });

  @override
  Widget build(BuildContext context) {
    if (text == null) {
      return Padding(
        padding: EdgeInsets.symmetric(vertical: verticalPadding),
        child: const Divider(
          color: AppColors.outlineVariant,
          thickness: 1,
          height: 1,
        ),
      );
    }

    return Padding(
      padding: EdgeInsets.symmetric(vertical: verticalPadding),
      child: Row(
        children: [
          const Expanded(
            child: Divider(
              color: AppColors.outlineVariant,
              thickness: 1,
              height: 1,
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.sm),
            child: Text(
              text!,
              style: AppTypography.labelMd.copyWith(
                color: AppColors.outline,
                fontWeight: FontWeight.w600,
                letterSpacing: 0.5,
              ),
            ),
          ),
          const Expanded(
            child: Divider(
              color: AppColors.outlineVariant,
              thickness: 1,
              height: 1,
            ),
          ),
        ],
      ),
    );
  }
}
