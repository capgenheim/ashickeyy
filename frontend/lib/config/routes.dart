import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../screens/home_screen.dart';
import '../screens/post_detail_screen.dart';
import '../screens/category_screen.dart';
import '../screens/search_screen.dart';
import '../screens/about_screen.dart';
import '../widgets/nav_bar.dart';

final GoRouter appRouter = GoRouter(
  routes: [
    ShellRoute(
      builder: (context, state, child) {
        return Scaffold(
          appBar: const AppNavBar(),
          body: child,
        );
      },
      routes: [
        GoRoute(
          path: '/',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: const HomeScreen(),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/post/:slug',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: PostDetailScreen(slug: state.pathParameters['slug']!),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/categories',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: const CategoryScreen(),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/categories/:slug',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: CategoryScreen(selectedSlug: state.pathParameters['slug']),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/search',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: SearchScreen(
              initialQuery: state.uri.queryParameters['q'],
            ),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/about',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: const AboutScreen(),
            transitionsBuilder: _fadeTransition,
          ),
        ),
      ],
    ),
  ],
);

Widget _fadeTransition(
  BuildContext context,
  Animation<double> animation,
  Animation<double> secondaryAnimation,
  Widget child,
) {
  return FadeTransition(
    opacity: CurveTween(curve: Curves.easeInOut).animate(animation),
    child: child,
  );
}
