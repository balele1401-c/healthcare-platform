import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../core/theme/app_colors.dart';
import '../../../../core/theme/app_radius.dart';
import '../../../../core/theme/app_spacing.dart';
import '../../../../core/theme/app_typography.dart';
import '../../../../shared/widgets/app_avatar.dart';
import '../../../../shared/widgets/app_button.dart';
import '../../../../shared/widgets/app_card.dart';
import '../../../../shared/widgets/app_snackbar.dart';
import '../../../../shared/widgets/app_text_field.dart';
import '../controllers/profile_controller.dart';

class EditProfileScreen extends ConsumerStatefulWidget {
  const EditProfileScreen({super.key});

  @override
  ConsumerState<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends ConsumerState<EditProfileScreen> {
  final _formKey = GlobalKey<FormState>();

  late TextEditingController _nameController;
  late TextEditingController _phoneController;
  late TextEditingController _heightController;
  late TextEditingController _weightController;
  late TextEditingController _addressController;
  late TextEditingController _emergencyNameController;
  late TextEditingController _emergencyPhoneController;

  late DateTime _selectedDob;
  late String _selectedGender;
  late String _selectedBloodType;

  final List<String> _bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
  final List<String> _genders = ['Female', 'Male', 'Other'];

  @override
  void initState() {
    super.initState();
    final profile = ref.read(patientProfileProvider);
    _nameController = TextEditingController(text: profile.fullName);
    _phoneController = TextEditingController(text: profile.phoneNumber);
    _heightController = TextEditingController(text: profile.heightCm);
    _weightController = TextEditingController(text: profile.weightKg);
    _addressController = TextEditingController(text: profile.address);
    _emergencyNameController = TextEditingController(text: profile.emergencyContactName);
    _emergencyPhoneController = TextEditingController(text: profile.emergencyContactPhone);

    _selectedDob = profile.dateOfBirth;
    _selectedGender = profile.gender;
    _selectedBloodType = profile.bloodType;
  }

  @override
  void dispose() {
    _nameController.dispose();
    _phoneController.dispose();
    _heightController.dispose();
    _weightController.dispose();
    _addressController.dispose();
    _emergencyNameController.dispose();
    _emergencyPhoneController.dispose();
    super.dispose();
  }

  void _handleSave() {
    if (_formKey.currentState!.validate()) {
      ref.read(patientProfileProvider.notifier).updateProfile(
            fullName: _nameController.text.trim(),
            phoneNumber: _phoneController.text.trim(),
            dateOfBirth: _selectedDob,
            gender: _selectedGender,
            bloodType: _selectedBloodType,
            heightCm: _heightController.text.trim(),
            weightKg: _weightController.text.trim(),
            address: _addressController.text.trim(),
            emergencyContactName: _emergencyNameController.text.trim(),
            emergencyContactPhone: _emergencyPhoneController.text.trim(),
          );

      AppSnackbar.showSuccess(context, 'Patient profile updated successfully.');
      Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    final profile = ref.watch(patientProfileProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: AppColors.onSurface),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Edit Profile',
          style: AppTypography.titleLarge.copyWith(
            color: AppColors.onSurface,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // 1. Avatar Update
              Center(
                child: Stack(
                  children: [
                    AppAvatar(
                      name: profile.fullName,
                      imageUrl: profile.avatarUrl,
                      size: 88,
                    ),
                    Positioned(
                      bottom: 0,
                      right: 0,
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: const BoxDecoration(
                          color: AppColors.primary,
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.camera_alt_rounded, color: AppColors.onPrimary, size: 16),
                      ),
                    ),
                  ],
                ),
              ),
              AppSpacing.gapVLg,

              // 2. Personal Fields
              Text(
                'Personal Information',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVSm,
              AppCard(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    AppTextField(
                      controller: _nameController,
                      label: 'Full Name',
                      hintText: 'Sarah Jenkins',
                      prefixIcon: const Icon(Icons.person_outline_rounded, size: 20, color: AppColors.onSurfaceVariant),
                      validator: (val) => val == null || val.trim().isEmpty ? 'Name is required' : null,
                    ),
                    AppSpacing.gapVMd,
                    AppTextField(
                      controller: _phoneController,
                      label: 'Phone Number',
                      hintText: '+1 (555) 019-2834',
                      prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.onSurfaceVariant),
                      keyboardType: TextInputType.phone,
                    ),
                    AppSpacing.gapVMd,

                    // Date of Birth Field
                    Text(
                      'Date of Birth',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    GestureDetector(
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: _selectedDob,
                          firstDate: DateTime(1930),
                          lastDate: DateTime.now(),
                        );
                        if (picked != null) {
                          setState(() {
                            _selectedDob = picked;
                          });
                        }
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md, vertical: 14),
                        decoration: BoxDecoration(
                          color: AppColors.surfaceContainerLowest,
                          borderRadius: AppRadius.radiusMd,
                          border: Border.all(color: AppColors.outlineVariant),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              DateFormat('MMMM d, yyyy').format(_selectedDob),
                              style: AppTypography.bodyMd.copyWith(color: AppColors.onSurface),
                            ),
                            const Icon(Icons.calendar_today_rounded, size: 18, color: AppColors.primary),
                          ],
                        ),
                      ),
                    ),
                    AppSpacing.gapVMd,

                    // Gender Selector
                    Text(
                      'Gender',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    Row(
                      children: _genders.map((g) {
                        final isSelected = _selectedGender == g;
                        return Padding(
                          padding: const EdgeInsets.only(right: AppSpacing.sm),
                          child: ChoiceChip(
                            label: Text(g),
                            selected: isSelected,
                            onSelected: (selected) {
                              if (selected) {
                                setState(() {
                                  _selectedGender = g;
                                });
                              }
                            },
                            selectedColor: AppColors.primary,
                            backgroundColor: AppColors.surfaceContainerLow,
                            labelStyle: AppTypography.labelMd.copyWith(
                              color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
                            ),
                            shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusFull),
                          ),
                        );
                      }).toList(),
                    ),
                    AppSpacing.gapVMd,

                    // Blood Type Selector
                    Text(
                      'Blood Type',
                      style: AppTypography.labelMd.copyWith(
                        color: AppColors.onSurface,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    AppSpacing.gapVSm,
                    Wrap(
                      spacing: AppSpacing.sm,
                      runSpacing: AppSpacing.sm,
                      children: _bloodTypes.map((type) {
                        final isSelected = _selectedBloodType == type;
                        return ChoiceChip(
                          label: Text(type),
                          selected: isSelected,
                          onSelected: (selected) {
                            if (selected) {
                              setState(() {
                                _selectedBloodType = type;
                              });
                            }
                          },
                          selectedColor: AppColors.primary,
                          backgroundColor: AppColors.surfaceContainerLow,
                          labelStyle: AppTypography.labelMd.copyWith(
                            color: isSelected ? AppColors.onPrimary : AppColors.onSurface,
                            fontWeight: FontWeight.w700,
                          ),
                          shape: RoundedRectangleBorder(borderRadius: AppRadius.radiusFull),
                        );
                      }).toList(),
                    ),
                    AppSpacing.gapVMd,

                    // Height & Weight Row
                    Row(
                      children: [
                        Expanded(
                          child: AppTextField(
                            controller: _heightController,
                            label: 'Height (cm)',
                            hintText: '168',
                            keyboardType: TextInputType.number,
                          ),
                        ),
                        AppSpacing.gapHMd,
                        Expanded(
                          child: AppTextField(
                            controller: _weightController,
                            label: 'Weight (kg)',
                            hintText: '64.5',
                            keyboardType: const TextInputType.numberWithOptions(decimal: true),
                          ),
                        ),
                      ],
                    ),
                    AppSpacing.gapVMd,
                    AppTextField(
                      controller: _addressController,
                      label: 'Home Address',
                      hintText: '742 Evergreen Terrace, Springfield',
                      prefixIcon: const Icon(Icons.home_outlined, size: 20, color: AppColors.onSurfaceVariant),
                      maxLines: 2,
                    ),
                  ],
                ),
              ),
              AppSpacing.gapVLg,

              // 3. Emergency Contact
              Text(
                'Emergency Contact Details',
                style: AppTypography.headlineSm.copyWith(
                  color: AppColors.onSurface,
                  fontWeight: FontWeight.w700,
                ),
              ),
              AppSpacing.gapVSm,
              AppCard(
                child: Column(
                  children: [
                    AppTextField(
                      controller: _emergencyNameController,
                      label: 'Contact Name & Relationship',
                      hintText: 'David Jenkins (Spouse)',
                      prefixIcon: const Icon(Icons.emergency_outlined, size: 20, color: AppColors.onSurfaceVariant),
                    ),
                    AppSpacing.gapVMd,
                    AppTextField(
                      controller: _emergencyPhoneController,
                      label: 'Emergency Phone Number',
                      hintText: '+1 (555) 019-5821',
                      prefixIcon: const Icon(Icons.phone_outlined, size: 20, color: AppColors.onSurfaceVariant),
                      keyboardType: TextInputType.phone,
                    ),
                  ],
                ),
              ),
              AppSpacing.gapV2Xl,
            ],
          ),
        ),
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.marginMobile,
          vertical: AppSpacing.md,
        ),
        decoration: BoxDecoration(
          color: AppColors.surface,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.05),
              offset: const Offset(0, -4),
              blurRadius: 10,
            ),
          ],
        ),
        child: SafeArea(
          child: AppButton(
            text: 'Save Profile Changes',
            prefixIcon: Icons.check_circle_outline_rounded,
            onPressed: _handleSave,
          ),
        ),
      ),
    );
  }
}
