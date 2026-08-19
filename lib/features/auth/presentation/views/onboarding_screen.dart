import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/routing/app_routes.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_shadows.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_text_button.dart';
import '../controllers/auth_controller.dart';

class OnboardingItem {
  final String title;
  final String description;
  final IconData icon;
  final Color iconBgColor;

  const OnboardingItem({
    required this.title,
    required this.description,
    required this.icon,
    required this.iconBgColor,
  });
}

class OnboardingScreen extends ConsumerStatefulWidget {
  const OnboardingScreen({super.key});

  @override
  ConsumerState<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends ConsumerState<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final List<OnboardingItem> _items = const [
    OnboardingItem(
      title: 'Find the Best Doctors',
      description: 'Book appointments and consult with top-rated specialists at your convenience.',
      icon: Icons.person_search_rounded,
      iconBgColor: AppColors.primaryContainer,
    ),
    OnboardingItem(
      title: 'Manage Your Records',
      description: 'Access your medical history, digital prescriptions, and vital trends anytime.',
      icon: Icons.health_and_safety_rounded,
      iconBgColor: AppColors.secondary,
    ),
    OnboardingItem(
      title: 'Smart Health Assistant',
      description: 'Get verified health education, symptom understanding, and clinical guidance.',
      icon: Icons.smart_toy_rounded,
      iconBgColor: AppColors.primary,
    ),
  ];

  Future<void> _completeOnboarding() async {
    final storage = ref.read(secureStorageProvider);
    await storage.setOnboardingCompleted(true);
    if (!mounted) return;
    context.go(AppRoutes.login);
  }

  void _nextPage() {
    if (_currentPage < _items.length - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 350),
        curve: Curves.easeInOut,
      );
    } else {
      _completeOnboarding();
    }
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 440),
            child: Column(
              children: [
                // Top Header with Skip
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: AppSpacing.sm),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        'HealthCare',
                        style: AppTypography.headlineMd.copyWith(
                          color: AppColors.primary,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                      if (_currentPage < _items.length - 1)
                        AppTextButton(
                          text: 'Skip',
                          onPressed: _completeOnboarding,
                        )
                      else
                        const SizedBox(width: 48),
                    ],
                  ),
                ),

                // Carousel Content
                Expanded(
                  child: PageView.builder(
                    controller: _pageController,
                    onPageChanged: (index) {
                      setState(() {
                        _currentPage = index;
                      });
                    },
                    itemCount: _items.length,
                    itemBuilder: (context, index) {
                      final item = _items[index];
                      return Padding(
                        padding: AppSpacing.paddingScreen,
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            // Illustration Container with glowing ambient backdrop
                            Stack(
                              alignment: Alignment.center,
                              children: [
                                Container(
                                  width: 220,
                                  height: 220,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    color: AppColors.primaryFixedDim.withValues(alpha: 0.3),
                                  ),
                                ),
                                Container(
                                  width: 170,
                                  height: 170,
                                  decoration: BoxDecoration(
                                    color: item.iconBgColor,
                                    shape: BoxShape.circle,
                                    boxShadow: AppShadows.cardAmbient,
                                  ),
                                  child: Center(
                                    child: Icon(
                                      item.icon,
                                      size: 80,
                                      color: AppColors.onPrimary,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                            AppSpacing.gapVXxl,
                            Text(
                              item.title,
                              style: AppTypography.headlineLgMobile.copyWith(
                                color: AppColors.onSurface,
                                fontWeight: FontWeight.w700,
                              ),
                              textAlign: TextAlign.center,
                            ),
                            AppSpacing.gapVSm,
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                              child: Text(
                                item.description,
                                style: AppTypography.bodyMd.copyWith(
                                  color: AppColors.onSurfaceVariant,
                                ),
                                textAlign: TextAlign.center,
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),

                // Bottom Indicators & Action Button Card
                Container(
                  padding: AppSpacing.paddingCard,
                  decoration: const BoxDecoration(
                    color: AppColors.surfaceContainerLowest,
                    borderRadius: AppRadius.radiusTopXl,
                    boxShadow: AppShadows.bottomNav,
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Progress Dots
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: List.generate(
                          _items.length,
                          (index) => AnimatedContainer(
                            duration: const Duration(milliseconds: 300),
                            margin: const EdgeInsets.symmetric(horizontal: AppSpacing.xs),
                            height: 8,
                            width: _currentPage == index ? 32 : 8,
                            decoration: BoxDecoration(
                              color: _currentPage == index ? AppColors.primary : AppColors.surfaceVariant,
                              borderRadius: AppRadius.radiusFull,
                            ),
                          ),
                        ),
                      ),
                      AppSpacing.gapVLg,
                      AppButton(
                        text: _currentPage == _items.length - 1 ? 'Get Started' : 'Next',
                        suffixIcon: Icons.arrow_forward_rounded,
                        onPressed: _nextPage,
                      ),
                    ],
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
