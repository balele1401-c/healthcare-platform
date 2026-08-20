import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_badge.dart';
import '../../../../shared/widgets/app_logo.dart';
import '../../../chat/presentation/views/conversation_list_screen.dart';
import '../../../doctor/presentation/views/doctor_search_screen.dart';
import '../../../profile/presentation/controllers/profile_controller.dart';
import '../../../profile/presentation/views/profile_screen.dart';
import '../../../records/presentation/views/medical_records_screen.dart';
import 'home_screen.dart';

class MainShellScreen extends ConsumerStatefulWidget {
  final int initialIndex;

  const MainShellScreen({super.key, this.initialIndex = 0});

  @override
  ConsumerState<MainShellScreen> createState() => _MainShellScreenState();
}

class _MainShellScreenState extends ConsumerState<MainShellScreen> {
  late int _currentIndex;

  final List<Widget> _screens = const [
    HomeScreen(),
    DoctorSearchScreen(),
    MedicalRecordsScreen(),
    ConversationListScreen(),
    ProfileScreen(),
  ];

  @override
  void initState() {
    super.initState();
    _currentIndex = widget.initialIndex;
  }

  void _onDestinationSelected(int index) {
    setState(() {
      _currentIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        if (constraints.maxWidth >= 900) {
          return _buildDesktopShell();
        } else {
          return _buildMobileShell();
        }
      },
    );
  }

  Widget _buildDesktopShell() {
    final profile = ref.watch(patientProfileProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: Row(
        children: [
          // Premium Desktop Navigation Sidebar (Linear / Stripe style)
          Container(
            width: 260,
            decoration: const BoxDecoration(
              color: AppColors.surface,
              border: Border(
                right: BorderSide(color: AppColors.outlineVariant, width: 0.8),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Top Brand Section
                Padding(
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  child: Row(
                    children: [
                      const AppLogo(iconSize: 36, fontSize: 20),
                      const Spacer(),
                      const AppBadge(text: 'PATIENT', variant: BadgeVariant.primary),
                    ],
                  ),
                ),
                const Divider(height: 1, color: AppColors.outlineVariant),
                AppSpacing.gapVMd,

                // Navigation Items
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                  child: Column(
                    children: [
                      _SidebarNavItem(
                        icon: Icons.dashboard_outlined,
                        activeIcon: Icons.dashboard_rounded,
                        label: 'Overview',
                        isSelected: _currentIndex == 0,
                        onTap: () => _onDestinationSelected(0),
                      ),
                      AppSpacing.gapVXs,
                      _SidebarNavItem(
                        icon: Icons.calendar_month_outlined,
                        activeIcon: Icons.calendar_month_rounded,
                        label: 'Find & Book Doctor',
                        isSelected: _currentIndex == 1,
                        onTap: () => _onDestinationSelected(1),
                      ),
                      AppSpacing.gapVXs,
                      _SidebarNavItem(
                        icon: Icons.receipt_long_outlined,
                        activeIcon: Icons.receipt_long_rounded,
                        label: 'Medical Records',
                        isSelected: _currentIndex == 2,
                        onTap: () => _onDestinationSelected(2),
                      ),
                      AppSpacing.gapVXs,
                      _SidebarNavItem(
                        icon: Icons.chat_bubble_outline_rounded,
                        activeIcon: Icons.chat_bubble_rounded,
                        label: 'Doctor Messages',
                        badgeCount: 2,
                        isSelected: _currentIndex == 3,
                        onTap: () => _onDestinationSelected(3),
                      ),
                      AppSpacing.gapVXs,
                      _SidebarNavItem(
                        icon: Icons.person_outline_rounded,
                        activeIcon: Icons.person_rounded,
                        label: 'Profile & Settings',
                        isSelected: _currentIndex == 4,
                        onTap: () => _onDestinationSelected(4),
                      ),
                    ],
                  ),
                ),

                const Spacer(),

                // AI Assistant Callout
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                  child: Container(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    decoration: BoxDecoration(
                      color: AppColors.primaryContainer.withValues(alpha: 0.5),
                      borderRadius: AppRadius.radiusMd,
                      border: Border.all(color: AppColors.primaryFixedDim, width: 0.8),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            const Icon(Icons.auto_awesome_rounded, size: 18, color: AppColors.primary),
                            AppSpacing.gapHSm,
                            Text(
                              'AI Health Assistant',
                              style: AppTypography.titleMedium.copyWith(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ],
                        ),
                        AppSpacing.gapVXs,
                        Text(
                          '24/7 symptom analysis and triage support.',
                          style: AppTypography.bodySm.copyWith(
                            color: AppColors.onSurfaceVariant,
                            fontSize: 12,
                          ),
                        ),
                        AppSpacing.gapVSm,
                        InkWell(
                          onTap: () => context.push(AppRoutes.aiAssistant),
                          child: Text(
                            'Launch Assistant →',
                            style: AppTypography.labelMd.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                AppSpacing.gapVMd,

                // User Profile Pill Footer
                const Divider(height: 1, color: AppColors.outlineVariant),
                Padding(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Row(
                    children: [
                      AppAvatar(
                        name: profile.fullName,
                        imageUrl: profile.avatarUrl,
                        size: 40,
                      ),
                      AppSpacing.gapHMd,
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              profile.fullName,
                              style: AppTypography.titleMedium.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w700,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                            Text(
                              'Verified Patient',
                              style: AppTypography.labelSm.copyWith(
                                color: AppColors.onSurfaceVariant,
                              ),
                            ),
                          ],
                        ),
                      ),
                      IconButton(
                        icon: const Icon(Icons.settings_outlined, size: 20, color: AppColors.outline),
                        onPressed: () => context.push(AppRoutes.settings),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // Main View Content
          Expanded(
            child: IndexedStack(
              index: _currentIndex,
              children: _screens,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMobileShell() {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: IndexedStack(
        index: _currentIndex,
        children: _screens,
      ),
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: AppColors.surface,
          border: Border(
            top: BorderSide(color: AppColors.outlineVariant, width: 0.8),
          ),
          boxShadow: AppShadows.bottomNav,
        ),
        child: NavigationBar(
          selectedIndex: _currentIndex,
          onDestinationSelected: _onDestinationSelected,
          backgroundColor: Colors.transparent,
          surfaceTintColor: Colors.transparent,
          indicatorColor: AppColors.primaryContainer,
          elevation: 0,
          destinations: const [
            NavigationDestination(
              icon: Icon(Icons.dashboard_outlined),
              selectedIcon: Icon(Icons.dashboard_rounded, color: AppColors.primary),
              label: 'Overview',
            ),
            NavigationDestination(
              icon: Icon(Icons.calendar_month_outlined),
              selectedIcon: Icon(Icons.calendar_month_rounded, color: AppColors.primary),
              label: 'Doctors',
            ),
            NavigationDestination(
              icon: Icon(Icons.receipt_long_outlined),
              selectedIcon: Icon(Icons.receipt_long_rounded, color: AppColors.primary),
              label: 'Records',
            ),
            NavigationDestination(
              icon: Badge(
                backgroundColor: AppColors.error,
                child: Icon(Icons.chat_bubble_outline_rounded),
              ),
              selectedIcon: Badge(
                backgroundColor: AppColors.error,
                child: Icon(Icons.chat_bubble_rounded, color: AppColors.primary),
              ),
              label: 'Messages',
            ),
            NavigationDestination(
              icon: Icon(Icons.person_outline_rounded),
              selectedIcon: Icon(Icons.person_rounded, color: AppColors.primary),
              label: 'Profile',
            ),
          ],
        ),
      ),
    );
  }
}

class _SidebarNavItem extends StatefulWidget {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  final bool isSelected;
  final int? badgeCount;
  final VoidCallback onTap;

  const _SidebarNavItem({
    required this.icon,
    required this.activeIcon,
    required this.label,
    required this.isSelected,
    this.badgeCount,
    required this.onTap,
  });

  @override
  State<_SidebarNavItem> createState() => _SidebarNavItemState();
}

class _SidebarNavItemState extends State<_SidebarNavItem> {
  bool _isHovering = false;

  @override
  Widget build(BuildContext context) {
    final activeBg = AppColors.primaryContainer.withValues(alpha: 0.6);
    final hoverBg = AppColors.surfaceContainerLow;

    return MouseRegion(
      onEnter: (_) => setState(() => _isHovering = true),
      onExit: (_) => setState(() => _isHovering = false),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 150),
        decoration: BoxDecoration(
          color: widget.isSelected
              ? activeBg
              : _isHovering
                  ? hoverBg
                  : Colors.transparent,
          borderRadius: AppRadius.radiusMd,
          border: widget.isSelected
              ? Border.all(color: AppColors.primaryFixedDim, width: 0.8)
              : null,
        ),
        child: InkWell(
          onTap: widget.onTap,
          borderRadius: AppRadius.radiusMd,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 11),
            child: Row(
              children: [
                Icon(
                  widget.isSelected ? widget.activeIcon : widget.icon,
                  size: 20,
                  color: widget.isSelected ? AppColors.primary : AppColors.onSurfaceVariant,
                ),
                AppSpacing.gapHMd,
                Expanded(
                  child: Text(
                    widget.label,
                    style: AppTypography.titleMedium.copyWith(
                      color: widget.isSelected ? AppColors.primary : AppColors.onSurface,
                      fontWeight: widget.isSelected ? FontWeight.w700 : FontWeight.w500,
                      fontSize: 14,
                    ),
                  ),
                ),
                if (widget.badgeCount != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                    decoration: BoxDecoration(
                      color: widget.isSelected ? AppColors.primary : AppColors.surfaceContainerHigh,
                      borderRadius: AppRadius.radiusFull,
                    ),
                    child: Text(
                      '${widget.badgeCount}',
                      style: TextStyle(
                        color: widget.isSelected ? Colors.white : AppColors.onSurfaceVariant,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
