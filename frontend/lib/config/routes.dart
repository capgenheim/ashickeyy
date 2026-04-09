import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../screens/home_screen.dart';
import '../screens/post_detail_screen.dart';
import '../screens/tag_screen.dart';
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
          path: '/tags',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: const TagScreen(),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/tags/:slug',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: TagScreen(selectedSlug: state.pathParameters['slug']),
            transitionsBuilder: _fadeTransition,
          ),
        ),
        GoRoute(
          path: '/search',
          pageBuilder: (context, state) => CustomTransitionPage(
            key: state.pageKey,
            child: SearchScreen(
              initialQuery: state.uri.queryParameters['q'],
              initialTag: state.uri.queryParameters['tag'],
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
