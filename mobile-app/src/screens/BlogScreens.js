/* The blog - the website's guides for car buyers: the list with its search,
   and each article. */
import { useEffect, useLayoutEffect, useState } from 'react';
import { ScrollView, StyleSheet, TextInput, View } from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, MAX_FONT, R, lift, useUI } from '../theme';
import * as api from '../api';
import { BLOG_PAGE } from '../api/static';
import { Button, Eyebrow, IconButton, Img, Press, T } from '../components/base';
import { Empty, PageHero, Wrap } from '../components/layout';
import Blocks from '../components/Blocks';
import { useGo } from '../nav';
import { shareBlog } from '../actions';

export function BlogScreen() {
  const ui = useUI();
  const go = useGo();
  const [q, setQ] = useState('');
  const [list, setList] = useState(null);

  useEffect(() => {
    const t = setTimeout(() => { api.getBlogs({ search: q }).then((r) => setList(r.items)); }, 200);
    return () => clearTimeout(t);
  }, [q]);

  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }} keyboardShouldPersistTaps="handled">
      <PageHero eyebrow={BLOG_PAGE.eyebrow} title={BLOG_PAGE.title} text={BLOG_PAGE.text} compact />
      <Wrap style={{ paddingTop: 18 }}>
        <View style={[styles.search, { minHeight: ui.tap(48) }]}>
          <Ionicons name="search" size={18} color={C.faint} style={{ marginLeft: 12 }} />
          <TextInput
            value={q}
            onChangeText={setQ}
            placeholder="Search blog"
            placeholderTextColor={C.faint}
            style={[styles.input, { fontSize: ui.t(15) }]}
            maxFontSizeMultiplier={MAX_FONT}
            selectionColor={C.red}
            returnKeyType="search"
          />
        </View>
        <Eyebrow style={{ marginTop: 20 }}>{BLOG_PAGE.listLabel}</Eyebrow>
        <T v="h2" style={{ marginBottom: 14 }}>{q ? 'Search results' : BLOG_PAGE.listTitle}</T>
        {list && !list.length ? (
          <Empty icon="newspaper-outline" title="No blog posts found." text={'Nothing matches "' + q + '".'} />
        ) : (
          <View style={ui.tablet ? styles.twoCols : null}>
            {(list || []).map((b) => (
              <Press key={b.slug} onPress={() => go.go('BlogPost', { slug: b.slug })} style={[styles.post, ui.tablet && { width: '48.5%' }]} accessibilityLabel={b.title}>
                <Img src={b.image} style={{ width: '100%', aspectRatio: 16 / 9 }} />
                <View style={{ padding: 16 }}>
                  <View style={styles.meta}>
                    <T v="small" color={C.red} weight="800">{b.date}</T>
                    <View style={styles.metaDot} />
                    <T v="small" color={C.muted} weight="600">{b.readTime}</T>
                  </View>
                  <T v="h3" style={{ marginTop: 8 }}>{b.title}</T>
                  {b.excerpt ? <T v="sub" color={C.muted} lines={3} style={{ marginTop: 6 }}>{b.excerpt}</T> : null}
                  <T v="small" color={C.red} weight="800" style={{ marginTop: 10 }}>Read More</T>
                </View>
              </Press>
            ))}
          </View>
        )}
        <View style={[styles.help]}>
          <T v="h3">{BLOG_PAGE.helpTitle}</T>
          <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{BLOG_PAGE.helpText}</T>
          <Button title="Contact SBK" kind="dark" size="sm" onPress={() => go.contact()} style={{ alignSelf: 'flex-start', marginTop: 12 }} />
        </View>
      </Wrap>
    </ScrollView>
  );
}

export function BlogPostScreen() {
  const ui = useUI();
  const go = useGo();
  const nav = useNavigation();
  const { slug } = useRoute().params || {};
  const [post, setPost] = useState(undefined);
  const [recent, setRecent] = useState([]);

  useEffect(() => {
    api.getBlog(slug).then(setPost);
    api.getRecentBlogs(slug).then(setRecent);
  }, [slug]);

  useLayoutEffect(() => {
    if (post) {
      nav.setOptions({ headerRight: () => <IconButton name="share-social-outline" onPress={() => shareBlog(post)} label="Share" /> });
    }
  }, [nav, post]);

  if (post === undefined) return <View style={{ flex: 1, backgroundColor: C.card }} />;
  if (!post) {
    return (
      <View style={{ flex: 1, backgroundColor: C.bg }}>
        <Empty icon="newspaper-outline" title="This article is not available" action="All Blogs" onAction={() => nav.goBack()} />
      </View>
    );
  }

  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.card }} contentContainerStyle={{ paddingBottom: 36 }}>
      <Img src={post.image} style={{ width: '100%', aspectRatio: ui.tablet ? 2.2 : 16 / 9 }} />
      <Wrap style={{ paddingTop: 18 }}>
        <Eyebrow>SBK Blog</Eyebrow>
        <T v="h1" size={ui.width < 360 ? 22 : 24}>{post.title}</T>
        <View style={[styles.meta, { marginTop: 10, marginBottom: 18 }]}>
          <Ionicons name="calendar-outline" size={14} color={C.muted} />
          <T v="small" color={C.muted} weight="600" style={{ marginLeft: 5 }}>{post.date}</T>
          <View style={styles.metaDot} />
          <Ionicons name="time-outline" size={14} color={C.muted} />
          <T v="small" color={C.muted} weight="600" style={{ marginLeft: 5 }}>{post.readTime}</T>
        </View>
        {post.body && post.body.length ? <Blocks blocks={post.body} /> : <T v="body" color={C.ink2}>More details will be added soon.</T>}

        {recent.length ? (
          <View style={{ marginTop: 26 }}>
            <T v="h3" style={{ marginBottom: 10 }}>Recent Posts</T>
            {recent.map((r) => (
              <Press key={r.slug} onPress={() => nav.push('BlogPost', { slug: r.slug })} style={styles.recent} accessibilityLabel={r.title}>
                <Img src={r.image} style={styles.recentImg} />
                <View style={{ flex: 1, marginLeft: 12 }}>
                  <T v="title" size={14} lines={2}>{r.title}</T>
                  <T v="small" color={C.muted} weight="500" style={{ marginTop: 3 }}>{r.date}</T>
                </View>
              </Press>
            ))}
          </View>
        ) : null}
        <View style={styles.help}>
          <T v="h3">{BLOG_PAGE.moreTitle}</T>
          <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{BLOG_PAGE.moreText}</T>
          <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 12 }}>
            <Button title="All Blogs" kind="dark" size="sm" onPress={() => go.go('Blog')} style={{ flex: 1 }} />
            <Button title="Browse Vehicles" size="sm" onPress={() => go.shop({})} style={{ flex: 1 }} />
          </View>
        </View>
      </Wrap>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  search: { flexDirection: 'row', alignItems: 'center', backgroundColor: C.card, borderRadius: R.md, borderWidth: 1, borderColor: C.line },
  input: { flex: 1, color: C.ink, paddingHorizontal: 10, paddingVertical: 8 },
  twoCols: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  post: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', marginBottom: 16, ...lift },
  meta: { flexDirection: 'row', alignItems: 'center' },
  metaDot: { width: 4, height: 4, borderRadius: 2, backgroundColor: C.faint, marginHorizontal: 8 },
  help: { backgroundColor: C.bg, borderRadius: R.lg, padding: 16, marginTop: 18 },
  recent: { flexDirection: 'row', alignItems: 'center', paddingVertical: 10, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  recentImg: { width: 86, height: 56, borderRadius: R.sm },
});
