import { createDirectus, rest } from '@directus/sdk';

const DIRECTUS_URL = import.meta.env.DIRECTUS_URL || 'http://localhost:8055';

export const directus = createDirectus(DIRECTUS_URL).with(rest());

export type Schema = {
  pages: Page[];
  services: Service[];
  testimonials: Testimonial[];
  slides: Slide[];
  blog_posts: BlogPost[];
  global: GlobalSettings;
};

export interface Page {
  id: number;
  slug: string;
  title: string;
  content: string;
  meta_description: string;
  hero_title?: string;
  hero_subtitle?: string;
  hero_image?: string;
  sort: number;
}

export interface Service {
  id: number;
  title: string;
  description: string;
  icon: string;
  sort: number;
}

export interface Testimonial {
  id: number;
  name: string;
  role: string;
  company: string;
  content: string;
  avatar?: string;
  sort: number;
}

export interface Slide {
  id: number;
  title: string;
  subtitle: string;
  description: string;
  image: string;
  cta_text?: string;
  cta_link?: string;
  sort: number;
}

export interface BlogPost {
  id: number;
  slug: string;
  title: string;
  excerpt: string;
  content: string;
  featured_image?: string;
  published_at: string;
  author: string;
  category: string;
}

export interface GlobalSettings {
  site_name: string;
  site_description: string;
  logo: string;
  email: string;
  phone: string;
  address: string;
  social_links: {
    linkedin?: string;
    twitter?: string;
    facebook?: string;
  };
}
