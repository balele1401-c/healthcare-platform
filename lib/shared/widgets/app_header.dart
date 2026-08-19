import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme/app_colors.dart';
import '../../core/theme/app_typography.dart';

class AppHeader extends StatelessWidget implements PreferredSizeWidget {
  final String? title;
  final Widget? titleWidget;
  final bool showBack;
  final bool showClose;
  final VoidCallback? onBack;
  final List<Widget>? actions;
  final PreferredSizeWidget? bottom;

  const AppHeader({
    super.key,
    this.title,
    this.titleWidget,
    this.showBack = true,
    this.showClose = false,
    this.onBack,
    this.actions,
    this.bottom,
  });

  @override
  Widget build(BuildContext context) {
    Widget? leading;

    if (showClose) {
      leading = IconButton(
        icon: const Icon(Icons.close_rounded, color: AppColors.onSurfaceVariant),
        onPressed: onBack ?? () => context.pop(),
      );
    } else if (showBack) {
      leading = IconButton(
        icon: const Icon(Icons.arrow_back_rounded, color: AppColors.primary),
        onPressed: onBack ?? () => context.pop(),
      );
    }

    return AppBar(
      leading: leading,
      title: titleWidget ??
          (title != null
              ? Text(
                  title!,
                  style: AppTypography.headlineSm.copyWith(
                    color: AppColors.onSurface,
                    fontWeight: FontWeight.w600,
                  ),
                )
              : null),
      actions: actions,
      bottom: bottom,
    );
  }

  @override
  Size get preferredSize => Size.fromHeight(kToolbarHeight + (bottom?.preferredSize.height ?? 0.0));
}
